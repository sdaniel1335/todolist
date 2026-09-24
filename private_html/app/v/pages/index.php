<div class="container py-3 task-page">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
  </div>

  <section class="task-section">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h6 mb-0">Todoist</h2>
      <span class="badge text-bg-secondary"><?php echo count($todoist_tasks); ?></span>
    </div>

    <?php if (!$has_todoist_api_key) { ?>
      <div class="alert alert-secondary py-2">
        No Todoist API key is set. <a href="<?php echo url('/todoist'); ?>">Set it here</a>.
      </div>
    <?php } elseif ($todoist_error !== '') { ?>
      <div class="alert alert-danger py-2">
        <?php echo htmlspecialchars($todoist_error, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php } ?>

    <?php if ($has_todoist_api_key && $todoist_error === '') { ?>
      <div class="task-table-wrap">
        <table class="table table-sm table-hover align-middle task-table">
          <thead>
            <tr>
              <th class="task-done-col"></th>
              <th>Task</th>
              <th class="task-status-col">Due</th>
              <th class="task-actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($todoist_tasks)) { ?>
              <tr class="task-empty-row">
                <td colspan="4" class="text-muted">No active Todoist tasks.</td>
              </tr>
            <?php } else { ?>
              <?php foreach ($todoist_tasks as $task) { ?>
                <?php
                  $task_id = isset($task['id']) ? (string) $task['id'] : '';
                  $is_today = isset($todoist_today_ids[$task_id]);
                  $label = isset($task['_managed_label'])
                    ? $task['_managed_label']
                    : '';
                  $due = '';

                  if (isset($task['due']) && is_array($task['due'])) {
                    if (isset($task['due']['date'])) {
                      $due = $task['due']['date'];
                    } elseif (isset($task['due']['string'])) {
                      $due = $task['due']['string'];
                    }
                  }

                  $form_id = 'todoist-task-' . preg_replace('/[^a-zA-Z0-9_-]/', '', $task_id);
                ?>
                <tr>
                  <td class="task-done-cell">
                    <form method="post" action="<?php echo url('/todoist/task'); ?>" class="m-0 js-checkbox-form" data-confirm="Mark this Todoist task as completed?">
                      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="action" value="complete">
                      <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id, ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="checkbox" class="form-check-input js-confirm-checkbox" aria-label="Complete task">
                    </form>
                  </td>
                  <td>
                    <form id="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>" method="post" action="<?php echo url('/todoist/task'); ?>" class="task-edit-form js-confirm-form">
                      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id, ENT_QUOTES, 'UTF-8'); ?>">

                      <input
                        type="text"
                        class="form-control form-control-sm task-content"
                        name="content"
                        value="<?php echo htmlspecialchars(isset($task['content']) ? $task['content'] : '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                      >

                      <select class="form-select form-select-sm task-label-select" name="label" aria-label="Label">
                        <option value=""<?php echo $label === '' ? ' selected' : ''; ?>>-</option>
                        <option value="B"<?php echo $label === 'B' ? ' selected' : ''; ?>>B</option>
                        <option value="P"<?php echo $label === 'P' ? ' selected' : ''; ?>>P</option>
                        <option value="W"<?php echo $label === 'W' ? ' selected' : ''; ?>>W</option>
                      </select>
                    </form>
                  </td>
                  <td>
                    <?php if ($is_today) { ?>
                      <span class="badge rounded-pill text-bg-primary task-today">Today</span>
                    <?php } elseif ($due !== '') { ?>
                      <span class="task-due"><?php echo htmlspecialchars($due, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php } else { ?>
                      <span class="text-muted">&mdash;</span>
                    <?php } ?>
                  </td>
                  <td>
                    <div class="task-actions">
                      <button
                        type="submit"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        name="action"
                        value="update"
                        class="btn btn-outline-primary btn-sm task-icon-btn"
                        data-confirm="Save changes to this Todoist task?"
                        title="Save"
                      ><i class="bi bi-check-lg"></i><span class="visually-hidden">Save</span></button>

                      <button
                        type="submit"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        name="action"
                        value="to_todolist"
                        class="btn btn-outline-secondary btn-sm task-icon-btn"
                        data-confirm="Move this task to Todolist and remove it from Todoist?"
                        title="Move to Todolist"
                      ><i class="bi bi-arrow-down"></i><span class="visually-hidden">Move to Todolist</span></button>

                      <button
                        type="submit"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        name="action"
                        value="delete"
                        class="btn btn-outline-danger btn-sm task-icon-btn"
                        data-confirm="Delete this Todoist task?"
                        title="Delete"
                      ><i class="bi bi-trash"></i><span class="visually-hidden">Delete</span></button>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </section>

  <section class="task-section">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h6 mb-0">Todolist</h2>
      <span class="badge text-bg-secondary"><?php echo count($todolist_tasks); ?></span>
    </div>

    <div class="task-table-wrap">
      <table class="table table-sm table-hover align-middle task-table">
        <thead>
          <tr>
            <th>Task</th>
            <th class="task-created-col">Created</th>
            <th class="task-actions-col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($todolist_tasks)) { ?>
            <tr class="task-empty-row">
              <td colspan="3" class="text-muted">No Todolist tasks.</td>
            </tr>
          <?php } else { ?>
            <?php foreach ($todolist_tasks as $task) { ?>
              <?php
                $stored_label = strtoupper(trim($task['label']));
                $label = in_array($stored_label, array('B', 'P', 'W'), true)
                  ? $stored_label
                  : '';
                $form_id = 'todolist-task-' . (int) $task['id'];
              ?>
              <tr>
                <td>
                  <form id="<?php echo $form_id; ?>" method="post" action="<?php echo url('/todolist/task'); ?>" class="task-edit-form js-confirm-form">
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="task_id" value="<?php echo (int) $task['id']; ?>">

                    <input
                      type="text"
                      class="form-control form-control-sm task-content"
                      name="content"
                      value="<?php echo htmlspecialchars($task['content'], ENT_QUOTES, 'UTF-8'); ?>"
                      required
                    >

                    <select class="form-select form-select-sm task-label-select" name="label" aria-label="Label">
                      <option value=""<?php echo $label === '' ? ' selected' : ''; ?>>-</option>
                      <option value="B"<?php echo $label === 'B' ? ' selected' : ''; ?>>B</option>
                      <option value="P"<?php echo $label === 'P' ? ' selected' : ''; ?>>P</option>
                      <option value="W"<?php echo $label === 'W' ? ' selected' : ''; ?>>W</option>
                    </select>
                  </form>
                </td>
                <td class="text-nowrap task-due">
                  <?php echo htmlspecialchars($task['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td>
                  <div class="task-actions">
                    <button
                      type="submit"
                      form="<?php echo $form_id; ?>"
                      name="action"
                      value="update"
                      class="btn btn-outline-primary btn-sm task-icon-btn"
                      data-confirm="Save changes to this Todolist task?"
                      title="Save"
                    ><i class="bi bi-check-lg"></i><span class="visually-hidden">Save</span></button>

                    <button
                      type="submit"
                      form="<?php echo $form_id; ?>"
                      name="action"
                      value="to_todoist"
                      class="btn btn-outline-secondary btn-sm task-icon-btn"
                      data-confirm="Move this task to Todoist Today and remove it from Todolist?"
                      title="Move to Todoist Today"
                      <?php echo !$has_todoist_api_key ? ' disabled' : ''; ?>
                    ><i class="bi bi-arrow-up"></i><span class="visually-hidden">Move to Todoist Today</span></button>

                    <button
                      type="submit"
                      form="<?php echo $form_id; ?>"
                      name="action"
                      value="delete"
                      class="btn btn-outline-danger btn-sm task-icon-btn"
                      data-confirm="Delete this Todolist task?"
                      title="Delete"
                    ><i class="bi bi-trash"></i><span class="visually-hidden">Delete</span></button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
