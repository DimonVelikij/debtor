(function (angular) {
    "use strict";

    angular
        .module('cms.core')
        .controller('CoreCtrl', CoreController);

    CoreController.$inject = [
        '$scope'
    ];

    function CoreController (
        $scope
    ) {

    }
})(angular);