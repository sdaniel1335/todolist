var App = {
  init: function () {
    if ( $('body').hasClass('js') ) {
      this.bindUIActions();
    }
  },

  bindUIActions: function () {
    $(document).on('click', '.js-confirm-form [type="submit"], [type="submit"][form]', function () {
      var button = $(this);
      var form = button.closest('form');
      var formId = button.attr('form');
      var message = button.data('confirm') || '';

      if (!form.length && formId) {
        form = $('#' + formId);
      }

      if (form.hasClass('js-confirm-form')) {
        form.data('submit-confirm', message);
      }
    });

    $(document).on('submit', '.js-confirm-form', function (event) {
      var form = $(this);
      var message = form.data('submit-confirm')
        || form.data('confirm')
        || 'Are you sure?';

      form.removeData('submit-confirm');

      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });

    $(document).on('change', '.js-confirm-checkbox', function () {
      var checkbox = this;
      var form = $(checkbox).closest('form');
      var message = form.data('confirm') || 'Are you sure?';

      if (!checkbox.checked) {
        return;
      }

      if (!window.confirm(message)) {
        checkbox.checked = false;
        return;
      }

      form.get(0).submit();
    });
  }
};
