(function ($) {
  /**
   * Triggers the corresponding quickedit contextual link for a quickedit-field,
   * that was dblclicked.
   */
  Drupal.behaviors.quickerEditTrigger = {
    attach: function (context, settings) {
      $('.quickedit-field').on('dblclick', function () {
        // Fetch the quickedit field id of the clicked element.
        var fieldID = $(this).data('quickedit-field-id');

        // Extract the entity id.
        var entityID = fieldID.split('/').slice(0, 2).join('/');

        // Find the entity element.
        var entityElement = $('.quickedit-processed').filter('[data-quickedit-entity-id="' + entityID + '"]');

        // Extract the instance id.
        var entityInstanceID = entityElement
          .get(0)
          .getAttribute('data-quickedit-entity-instance-id');

        // Enable the currently processed quickedit entity.
        var entityModel = Drupal.quickedit.collections.entities.findWhere({
          entityID: entityID,
          entityInstanceID: entityInstanceID
        });
        entityModel.set('state', 'launching');
      });
    }
  };
})(jQuery);
