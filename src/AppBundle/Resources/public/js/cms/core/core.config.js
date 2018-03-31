(function (angular) {
    "use strict";

    angular
        .module('cms.core')
        .config(config);

    config.$inject = [
        '$interpolateProvider'
    ];

    function config (
        $interpolateProvider
    ) {
        $interpolateProvider
            .startSymbol('[[')
            .endSymbol(']]');
    }

})(angular);