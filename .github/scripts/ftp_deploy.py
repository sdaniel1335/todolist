"""Manually deploy tracked application files using plain, passive FTP."""

import ftplib
import os
from pathlib import Path, PurePosixPath
import posixpath
import subprocess
import sys

ROOT_FILES = {"index.php", ".htaccess"}


def application_files(repo):
    """Use an allowlist so local config, logs and deployment files stay private."""
    tracked = subprocess.check_output(
        ["git", "ls-files", "-z"], cwd=repo
    ).decode("utf-8").split("\0")
    selected = []
    for name in sorted(filter(None, tracked)):
        path = PurePosixPath(name)
        allowed = (
            name in ROOT_FILES
            or name.startswith("public_html/")
            or name.startswith("private_html/app/")
            or name.startswith("private_html/framework/")
        )
        if not allowed or name == "private_html/app/config/db.php":
            continue
        if path.is_absolute() or ".." in path.parts:
            raise ValueError("Invalid application path")
        local = repo / name
        if local.is_symlink() or not local.is_file():
            raise ValueError("Application file must be a regular file: " + name)
        if not local.resolve().is_relative_to(repo.resolve()):
            raise ValueError("Application file is outside the checkout")
        selected.append(name)
    if not ROOT_FILES.issubset(selected):
        raise ValueError("Application entry point or rewrite rules are missing")
    return selected


def deploy(ftp, repo, target, files, upload):
    # The target must already exist. Never guess another location or FTP root.
    try:
        ftp.cwd(target)
    except ftplib.error_perm:
        raise RuntimeError(
            "Cannot enter FTP_DEPLOY_DIR. Check its FTP-visible path; "
            "a jailed account may need /public_html/todolist/ instead."
        ) from None
    root = ftp.pwd()
    ftp.nlst()  # Validate the passive data connection before writing anything.
    print("FTP login, target directory and passive data connection verified.")
    for name in files:
        print(("Upload: " if upload else "Would upload: ") + name)
    if not upload:
        print("Check only: no files uploaded, changed or deleted.")
        return
    known_dirs = {"."}
    for name in files:
        parent = str(PurePosixPath(name).parent)
        if parent not in known_dirs:
            current = root
            for part in PurePosixPath(parent).parts:
                current = posixpath.join(current, part)
                try:
                    ftp.cwd(current)
                except ftplib.error_perm:
                    ftp.mkd(current)
                    ftp.cwd(current)
            known_dirs.add(parent)
        ftp.cwd(posixpath.join(root, parent))
        with (repo / name).open("rb") as source:
            ftp.storbinary("STOR " + PurePosixPath(name).name, source)
        if ftp.size(PurePosixPath(name).name) != (repo / name).stat().st_size:
            raise RuntimeError("Uploaded file size mismatch: " + name)
    print("Uploaded and size-checked " + str(len(files)) + " application files.")


def main():
    required = ["FTP_HOST", "FTP_USER", "FTP_PASSWORD", "FTP_DEPLOY_DIR"]
    missing = [name for name in required if not os.environ.get(name)]
    if missing:
        raise RuntimeError("Missing configuration: " + ", ".join(missing))
    upload_flag = os.environ.get("DEPLOY_UPLOAD", "false").lower()
    if upload_flag not in {"true", "false"}:
        raise ValueError("DEPLOY_UPLOAD must be true or false")
    repo = Path(__file__).resolve().parents[2]
    files = application_files(repo)
    with ftplib.FTP(timeout=30) as ftp:
        ftp.connect(os.environ["FTP_HOST"], 21)
        ftp.login(os.environ["FTP_USER"], os.environ["FTP_PASSWORD"])
        ftp.set_pasv(True)
        deploy(ftp, repo, os.environ["FTP_DEPLOY_DIR"], files, upload_flag == "true")


if __name__ == "__main__":
    try:
        main()
    except (OSError, ftplib.Error, RuntimeError, ValueError) as error:
        # Never print authentication responses, credentials or tracebacks.
        if isinstance(error, (ftplib.Error, OSError)):
            print("FTP operation failed (" + type(error).__name__ + "). "
                  "Check connectivity, credentials and permissions.", file=sys.stderr)
        else:
            print(str(error), file=sys.stderr)
        sys.exit(1)
