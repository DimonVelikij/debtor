(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorCtrl', DebtorCtrl);

    DebtorCtrl.$inject = [
        '$scope'
    ];

    function DebtorCtrl(
        $scope
    ) {
        console.log('debtor');
    }

})(angular);