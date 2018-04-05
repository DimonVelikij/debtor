(function (angular) {
    "use strict";

    angular
        .module('cms.core')
        .directive('backend', BackendValidatorDirective);

    BackendValidatorDirective.$inject = [];

    function BackendValidatorDirective () {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attr, ngModel) {
                ngModel.$parsers.push(function (value) {
                    if (value) {
                        ngModel.$setValidity('backend', true);
                    }

                    return value;
                });
            }
        };
    }

})(angular);