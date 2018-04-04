(function (angular) {
    "use strict";

    angular
        .module('cms.debtor')
        .controller('DebtorCreateCtrl', DebtorCreateController);

    DebtorCreateController.$inject = [
        '$scope',
        '$http',
        'Initializer'
    ];

    function DebtorCreateController (
        $scope,
        $http,
        Initializer
    ) {
        $http.get(Initializer.Path.AdminDebtorTypes)
            .then(function (response) {
                console.log(response);
            }, function () {

            });
    }

})(angular);