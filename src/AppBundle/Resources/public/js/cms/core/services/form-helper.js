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
            forceDirty: forceDirty,
            forcePristine: forcePristine,
            showBackendErrors: showBackendErrors
        };

        /**
         * проставляем всем полям dirty
         * @param form
         */
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

        /**
         * проставляем всем полям pristine
         * @param form
         */
        function forcePristine (form) {
            if (!form) {
                return;
            }

            if (form.$dirty) {
                form.$setPristine();
            }

            _.forEach(form, function (field, name) {
                if (name[0] !== '$' && field.$dirty && field.$setPristine) {
                    field.$setPristine();
                }
            });
        }

        /**
         * показываем ошибки с бэкенда
         * @param errors
         * @param form
         */
        function showBackendErrors (errors, form) {
            _.forEach(errors, function (error, fieldName) {
                form[fieldName].errorMessages = {
                    backend: error
                };
                form[fieldName].$setValidity('backend', false);
            });
        }
    }
})(angular);