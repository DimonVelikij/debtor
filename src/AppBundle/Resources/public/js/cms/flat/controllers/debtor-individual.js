(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorIndividualCtrl', DebtorIndividualCtrl);

    DebtorIndividualCtrl.$inject = [
        '$scope',
        '$http',
        'Initializer'
    ];

    function DebtorIndividualCtrl (
        $scope,
        $http,
        Initializer
    ) {
        /**
         * загрузка статусов собственности
         */
        $http.get(Initializer.Path.AdminDebtorOwnershipStatuses)
            .then(function (response) {
                $scope.ownershipStatuses = response.data;
            })
            .finally(function () {
                $scope.currentDebtor.ownershipStatus = null;
                $scope.currentDebtor.ownershipSubStatus = null;
            });
    }

})(angular);