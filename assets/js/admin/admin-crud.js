//Confirmation suppression
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Confirmation de suppression
        $('.button-link-delete').on('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
                return false;
            }
        });
    });
    
})(jQuery);