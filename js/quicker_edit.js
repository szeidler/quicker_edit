(function ($) {
  /**
   * Triggers the corresponding quickedit contextual link for a quickedit-field,
   * that was dblclicked.
   */
  Drupal.behaviors.quickerEditTrigger = {
    attach: function (context, settings) {
      $('[data-quickedit-field-id]', context).on('dblclick', function () {
        let $fieldElement = $(this);

        // Find the entity element.
        let $entityElement = $fieldElement.closest('[data-quickedit-entity-id]');

        // Enable the Quick Edit by programatically triggering the contextual link.
        $entityElement.find('[data-contextual-id] .quickedit > a').trigger('click');

        // Programmatically trigger a click once again on the field, to start
        // editing the field. We need a timeout, to make sure the contextual
        // trigger callback chain was finished successfully.
        setTimeout(function () {
          $fieldElement.trigger('click');
        }, 100);
      });
    }
  };
})(jQuery);
