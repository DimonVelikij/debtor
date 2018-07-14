(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .directive('logMessage', LogMessageDirective);

    LogMessageDirective.$inject = [
        '$compile'
    ];

    function LogMessageDirective (
        $compile
    ) {
        return {
            restrict: 'E',
            scope: {
                log: '='
            },
            replace: false,
            link: function (scope, element) {
                if (!scope.log || !scope.log.data) {
                    return;
                }

                element.html($compile('<div>' + scope.log.data + '</div>')(scope));
            }
        };
    }

})(angular);