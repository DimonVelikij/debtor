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
            $http.get(Initializer.Path.AdminDebtors)
        ]).then(function (response) {
            console.log(response);
        });

        $scope.debtors = [
            {
                id: 1,
                name: 'Иванов Иван',
                status: 'Собственник',
                phone: '+79223334455'
            },
            {
                id: 2,
                name: 'Петров Петр',
                status: 'Собственник',
                phone: '+79223334466'
            }
        ];

        $scope.currentDebtorInfo = {};

        $scope.toggleDebtorInfo = function (debtor) {
            if ($scope.currentDebtorInfo[debtor.id]) {
                delete $scope.currentDebtorInfo[debtor.id];

                return;
            }

            $scope.currentDebtorInfo[debtor.id] = debtor;
        };

        $scope.isShowDebtorInfo = function (debtor) {
            return $scope.currentDebtorInfo[debtor.id];
        };
    }

})(angular);