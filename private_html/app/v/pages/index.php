<?php
  $badge_date = new DateTime('now', new DateTimeZone('Europe/Budapest'));
  $relative_due_dates = array($badge_date->format('Y-m-d') => lang('TODAY'));
  $badge_date->modify('-1 day');
  $relative_due_dates[$badge_date->format('Y-m-d')] = lang('YESTERDAY');
  $badge_date->modify('+2 days');
  $relative_due_dates[$badge_date->format('Y-m-d')] = lang('TOMORROW');
?>
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
        <?php echo lang_html('NO_TODOIST_API_KEY_IS_SET'); ?> <a href="<?php echo url('/todoist'); ?>"><?php echo lang_html('SET_IT_HERE'); ?></a>.
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
              <th><?php echo lang_html('TASK'); ?></th>
              <th class="task-status-col"><?php echo lang_html('DUE'); ?></th>
              <th class="task-actions-col"><?php echo lang_html('ACTIONS'); ?></th>
            </tr>
          </thead>
          <tbody>
            <tr class="task-create-row">
              <td></td>
              <td>
                <form id="todoist-task-create" method="post" action="<?php echo url('/todoist/task'); ?>" class="task-edit-form">
                  <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="action" value="create">

                  <input
                    type="text"
                    class="form-control form-control-sm task-content"
                    name="content"
                    placeholder="<?php echo lang_html('NEW_TODOIST_TASK'); ?>"
                    aria-label="<?php echo lang_html('NEW_TODOIST_TASK'); ?>"
                    required
                  >

                  <select class="form-select form-select-sm task-label-select" name="label" aria-label="<?php echo lang_html('LABEL'); ?>">
                    <option value="">-</option>
                    <option value="B">B</option>
                    <option value="P">P</option>
                    <option value="W">W</option>
                  </select>
                </form>
              </td>
              <td>
                <span class="badge rounded-pill text-bg-primary task-today"><?php echo lang_html('TODAY'); ?></span>
              </td>
              <td>
                <div class="task-actions">
                  <button
                    type="submit"
                    form="todoist-task-create"
                    class="btn btn-primary btn-sm task-icon-btn"
                    title="<?php echo lang_html('ADD_TO_TODOIST_TODAY'); ?>"
                  ><i class="bi bi-plus-lg"></i><span class="visually-hidden"><?php echo lang_html('ADD_TO_TODOIST_TODAY'); ?></span></button>
                </div>
              </td>
            </tr>
            <?php if (empty($todoist_tasks)) { ?>
              <tr class="task-empty-row">
                <td colspan="4" class="text-muted"><?php echo lang_html('NO_ACTIVE_TODOIST_TASKS'); ?></td>
              </tr>
            <?php } else { ?>
              <?php foreach ($todoist_tasks as $task) { ?>
                <?php
                  $task_id = isset($task['id']) ? (string) $task['id'] : '';
                  $label = isset($task['_managed_label'])
                    ? $task['_managed_label']
                    : '';
                  $due = '';

                  if (isset($task['due']) && is_array($task['due'])) {
                    if (isset($task['due']['date'])) {
                      $due = substr($task['due']['date'], 0, 10);
                    }
                  }

                  $form_id = 'todoist-task-' . preg_replace('/[^a-zA-Z0-9_-]/', '', $task_id);
                ?>
                <tr>
                  <td class="task-done-cell">
                    <form method="post" action="<?php echo url('/todoist/task'); ?>" class="m-0 js-checkbox-form" data-confirm="<?php echo lang_html('MARK_THIS_TODOIST_TASK_AS_COMPLETED'); ?>">
                      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="action" value="complete">
                      <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id, ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="checkbox" class="form-check-input js-confirm-checkbox" aria-label="<?php echo lang_html('COMPLETE_TASK'); ?>">
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

                      <select class="form-select form-select-sm task-label-select" name="label" aria-label="<?php echo lang_html('LABEL'); ?>">
                        <option value=""<?php echo $label === '' ? ' selected' : ''; ?>>-</option>
                        <option value="B"<?php echo $label === 'B' ? ' selected' : ''; ?>>B</option>
                        <option value="P"<?php echo $label === 'P' ? ' selected' : ''; ?>>P</option>
                        <option value="W"<?php echo $label === 'W' ? ' selected' : ''; ?>>W</option>
                      </select>
                    </form>
                  </td>
                  <td>
                    <div class="task-due-editor">
                      <input
                        type="date"
                        class="form-control form-control-sm task-due-input"
                        name="due_date"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        value="<?php echo htmlspecialchars($due, ENT_QUOTES, 'UTF-8'); ?>"
                        aria-label="<?php echo lang_html('DUE_DATE'); ?>"
                      >
                      <?php if (isset($relative_due_dates[$due])) { ?>
                        <span class="badge rounded-pill task-today <?php echo $relative_due_dates[$due] === lang('YESTERDAY') ? 'text-bg-warning' : ($relative_due_dates[$due] === lang('TODAY') ? 'text-bg-primary' : 'text-bg-secondary'); ?>"><?php echo $relative_due_dates[$due]; ?></span>
                      <?php } ?>
                    </div>
                  </td>
                  <td>
                    <div class="task-actions">
                      <button
                        type="submit"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        name="action"
                        value="update"
                        class="btn btn-outline-primary btn-sm task-icon-btn"
                        data-confirm="<?php echo lang_html('SAVE_CHANGES_TO_THIS_TODOIST_TASK'); ?>"
                        title="<?php echo lang_html('SAVE'); ?>"
                      ><i class="bi bi-check-lg"></i><span class="visually-hidden"><?php echo lang_html('SAVE'); ?></span></button>

                      <button
                        type="submit"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        name="action"
                        value="to_todolist"
                        class="btn btn-outline-secondary btn-sm task-icon-btn"
                        data-confirm="<?php echo lang_html('MOVE_THIS_TASK_TO_TODOLIST_AND_REMOVE_IT_FROM_TODOIST'); ?>"
                        title="<?php echo lang_html('MOVE_TO_TODOLIST'); ?>"
                      ><i class="bi bi-arrow-down"></i><span class="visually-hidden"><?php echo lang_html('MOVE_TO_TODOLIST'); ?></span></button>

                      <button
                        type="submit"
                        form="<?php echo htmlspecialchars($form_id, ENT_QUOTES, 'UTF-8'); ?>"
                        name="action"
                        value="delete"
                        class="btn btn-outline-danger btn-sm task-icon-btn"
                        data-confirm="<?php echo lang_html('DELETE_THIS_TODOIST_TASK'); ?>"
                        title="<?php echo lang_html('DELETE'); ?>"
                      ><i class="bi bi-trash"></i><span class="visually-hidden"><?php echo lang_html('DELETE'); ?></span></button>
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
            <th><?php echo lang_html('TASK'); ?></th>
            <th class="task-created-col"><?php echo lang_html('CREATED'); ?></th>
            <th class="task-actions-col"><?php echo lang_html('ACTIONS'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr class="task-create-row">
            <td>
              <form id="todolist-task-create" method="post" action="<?php echo url('/todolist/task'); ?>" class="task-edit-form">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

                <input
                  type="text"
                  class="form-control form-control-sm task-content"
                  name="content"
                  placeholder="<?php echo lang_html('NEW_TASK'); ?>"
                  required
                >

                <select class="form-select form-select-sm task-label-select" name="label" aria-label="<?php echo lang_html('LABEL'); ?>">
                  <option value="">-</option>
                  <option value="B">B</option>
                  <option value="P">P</option>
                  <option value="W">W</option>
                </select>
              </form>
            </td>
            <td class="text-nowrap task-due">
              &mdash;
            </td>
            <td>
              <div class="task-actions">
                <button
                  type="submit"
                  form="todolist-task-create"
                  name="action"
                  value="create"
                  class="btn btn-primary btn-sm task-icon-btn"
                  title="<?php echo lang_html('ADD'); ?>"
                ><i class="bi bi-plus-lg"></i><span class="visually-hidden"><?php echo lang_html('ADD'); ?></span></button>
              </div>
            </td>
          </tr>
          <?php if (empty($todolist_tasks)) { ?>
            <tr class="task-empty-row">
              <td colspan="3" class="text-muted"><?php echo lang_html('NO_TODOLIST_TASKS'); ?></td>
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

                    <select class="form-select form-select-sm task-label-select" name="label" aria-label="<?php echo lang_html('LABEL'); ?>">
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
                      data-confirm="<?php echo lang_html('SAVE_CHANGES_TO_THIS_TODOLIST_TASK'); ?>"
                      title="<?php echo lang_html('SAVE'); ?>"
                    ><i class="bi bi-check-lg"></i><span class="visually-hidden"><?php echo lang_html('SAVE'); ?></span></button>

                    <button
                      type="submit"
                      form="<?php echo $form_id; ?>"
                      name="action"
                      value="to_todoist"
                      class="btn btn-outline-secondary btn-sm task-icon-btn"
                      data-confirm="<?php echo lang_html('MOVE_THIS_TASK_TO_TODOIST_TODAY_AND_REMOVE_IT_FROM_TODOLIST'); ?>"
                      title="<?php echo lang_html('MOVE_TO_TODOIST_TODAY'); ?>"
                      <?php echo !$has_todoist_api_key ? ' disabled' : ''; ?>
                    ><i class="bi bi-arrow-up"></i><span class="visually-hidden"><?php echo lang_html('MOVE_TO_TODOIST_TODAY'); ?></span></button>

                    <button
                      type="submit"
                      form="<?php echo $form_id; ?>"
                      name="action"
                      value="delete"
                      class="btn btn-outline-danger btn-sm task-icon-btn"
                      data-confirm="<?php echo lang_html('DELETE_THIS_TODOLIST_TASK'); ?>"
                      title="<?php echo lang_html('DELETE'); ?>"
                    ><i class="bi bi-trash"></i><span class="visually-hidden"><?php echo lang_html('DELETE'); ?></span></button>
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
