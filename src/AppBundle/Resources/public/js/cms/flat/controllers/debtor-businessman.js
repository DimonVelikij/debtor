(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorBusinessmanCtrl', DebtorBusinessmanCtrl);

    DebtorBusinessmanCtrl.$inject = [
        '$scope',
        '$http',
        'Initializer'
    ];

    function DebtorBusinessmanCtrl (
        $scope,
        $http,
        Initializer
    ) {
        /**
         * загрузка статусов собственности
         */
        $http.get(Initializer.Path.AdminDebtorOwnershipStatuses + '/owner')
            .then(function (response) {
                $scope.ownershipStatuses = response.data;
            })
            .finally(function () {
                $scope.currentDebtor.ownershipStatus = null;
                $scope.currentDebtor.ownershipSubStatus = null;
            });
    }

})(angular);