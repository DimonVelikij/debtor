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
            debtorType: null,

            name: null,
            ownershipStatus: null,
            ogrn: null,
            inn: null,
            location: null,
            phone: null,
            email: null,

            dateOfBirth: null,
            placeOfBirth: null,
            ogrnip: null,
            bossName: null,
            bossPosition: null,

            startDebtPeriod: null,
            endDebtPeriod: null,

            dateFillDebt: null,
            sumDebt: null,
            periodAccruedDebt: null,
            periodPayDebt: null,

            dateFillFine: null,
            sumFine: null,
            periodAccruedFine: null,
            periodPayFine: null
        };

        /**
         * подгружаем типы должников
         */
        $http.get(Initializer.Path.AdminDebtorTypes)
            .then(function (response) {
                $scope.debtorTypes = _.merge(response.data);
            });

        $scope.$watch('formData.debtorType', function (newDebtorType) {
            if (!newDebtorType) {
                return;
            }

            loadOwnershipStatuses();
        });

        /**
         * загрузка статусов собстенности
         */
        function loadOwnershipStatuses () {
            var selectedOwnershipStatusId = $scope.formData.ownershipStatus ?
                $scope.formData.ownershipStatus.id :
                null,
                url = Initializer.Path.AdminDebtorOwnershipStatuses + '?debtor_type=' + $scope.formData.debtorType.alias;

            url += selectedOwnershipStatusId ? '&selected_status_id=' + selectedOwnershipStatusId : '';

            $http.get(url)
                .then(function (response) {
                    // console.log(response);
                });
        }

        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            $http.post(Initializer.Path.AdminDebtorSave, $scope.formData)
                .then(function (response) {
                    if (response.data.success) {

                    } else {
                        FormHelper.showBackendErrors(response.data.errors, form);
                    }
                });
        };
    }

})(angular);