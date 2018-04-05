(function (angular) {
    "use strict";

    angular
        .module('cms.core')
        .factory('FormHelper', FormHelper);

    FormHelper.$inject = [
        '_'
    ];

    function FormHelper (
        _
    ) {
        return {
            forceDirty: forceDirty
        };

        function forceDirty (form) {
            if (!form) {
                return;
            }

            if (form.$pristine) {
                form.$setDirty();
            }

            _.forEach(form, function (field, name) {
                if (name[0] !== '$' && field.$pristine && field.$setDirty) {
                    field.$setDirty();
                }
            });
        }
    }
})(angular);