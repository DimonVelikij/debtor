(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorCtrl', DebtorCtrl);

    DebtorCtrl.$inject = [
        '$scope',
        '$http',
        '$q',
        'Initializer'
    ];

    function DebtorCtrl(
        $scope,
        $http,
        $q,
        Initializer
    ) {
        $q.all([
            $http.get(Initializer.Path.AdminDebtorList),
            $http.get(Initializer.Path.AdminDebtorTypes)
        ]).then(function (response) {
            $scope.debtors = response[0].data;
            $scope.debtorTypes = response[1].data;
        });
    }

})(angular);