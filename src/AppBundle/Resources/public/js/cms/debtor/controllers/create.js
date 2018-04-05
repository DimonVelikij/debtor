(function (angular) {
    "use strict";

    angular
        .module('cms.debtor')
        .controller('DebtorCreateCtrl', DebtorCreateController);

    DebtorCreateController.$inject = [
        '$scope',
        '$http',
        'Initializer',
        'FormHelper'
    ];

    function DebtorCreateController (
        $scope,
        $http,
        Initializer,
        FormHelper
    ) {
        $scope.formData = {
            debtorType: null
        };

        /**
         * подгружаем типы должников
         */
        $http.get(Initializer.Path.AdminDebtorTypes)
            .then(function (response) {
                $scope.debtorTypes = _.merge(response.data);
            });

        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            /*form['type'].errorMessages = {
                backend: 'test'
            };
            form['type'].$setValidity('backend', false);
            вынести в FormHelper.showBackendErrors
            */
        };
    }

})(angular);