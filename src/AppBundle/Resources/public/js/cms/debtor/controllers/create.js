(function (angular) {
    "use strict";

    angular
        .module('cms.debtor')
        .controller('DebtorCreateCtrl', DebtorCreateController);

    DebtorCreateController.$inject = [
        '$scope',
        '$http',
        '$q',
        '$window',
        'Initializer',
        'FormHelper'
    ];

    function DebtorCreateController (
        $scope,
        $http,
        $q,
        $window,
        Initializer,
        FormHelper
    ) {
        $scope.formData = {
            debtorType: null,
            company: null,
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

        $q.all([
            $http.get(Initializer.Path.AdminDebtorTypes),
            $http.get(Initializer.Path.AdminDebtorCompanies)
        ]).then(function (response) {
            $scope.debtorTypes = response[0].data;
            $scope.companies = response[1].data;

            if ($scope.companies.length === 1) {
                $scope.formData.company = $scope.companies[0].id;
            }
        });

        /*$scope.$watch('formData.debtorType', function (newDebtorType) {
            if (!newDebtorType) {
                return;
            }

            loadOwnershipStatuses();
        });

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
        }*/

        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            $http.post(Initializer.Path.AdminDebtorSave, $scope.formData)
                .then(function (response) {
                    if (response.data.success) {
                        $window.location.href = response.data.edit;
                    } else {
                        FormHelper.showBackendErrors(response.data.errors, form);
                    }
                });
        };
    }

})(angular);