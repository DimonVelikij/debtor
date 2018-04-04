(function (angular) {
    "use strict";

    angular
        .module('cms.core')
        .factory('Initializer', InitializerService)
        .directive('script', InitializerDirective)
    ;

    InitializerService.$inject = ['$timeout', '$q'];
    function InitializerService($timeout, $q) {
        var InitializerService = {
            startup: startup
        };

        return InitializerService;

        /**
         * Инициализация свойств скопа значениями по умолчанию (для target="scope"). Вызов через промайз колбека по окончанию работы директив.
         * Пример:
         * <script type="text/ng-init" target="scope" name="auction">
         *     { id: 'adfcf3232fc23' }
         * </script>
         *
         * angular.controller('AuctionCtrl, function($scope) {
         *     Initializer
         *         .startup($scope, 'auction', {}) // здесь $scope.auction = {};
         *         .then(function() {
         *             // Здесь уже отработала директива инициализации
         *             // $scope.auction = { id: 'adfcf3232fc23' };
         *         })
         *     ;
         * });
         *
         * @param scope
         * @param {Array|string} keys Список свойств/свойство, которые будут инициализированы у скопа
         * @param {*} defs Список дефолтовых значений/дефолтовое значение, которое будет первоначально назначено свойствам
         * @returns {promise}
         */
        function startup(scope, keys, defs) {
            var deferred = $q.defer();

            // инициализация дефолтовыми значениями
            scope && keys && startup(keys, defs);

            // после таймаута директивы отработают, в скопе появятся значения, запускаем успешный промайз
            $timeout(deferred.resolve);

            return deferred.promise;

            function startup(keys, src) {
                if(angular.isString(keys)) {
                    scope[keys] = src;
                }
                else {
                    angular.forEach(keys, function (key, index) {
                        scope[key] = src[index];
                    });
                }
            }
        }
    }

    InitializerDirective.$inject = ['$parse', '$rootScope', 'Initializer', '_'];
    function InitializerDirective($parse, $rootScope, Initializer, _) {
        var InitializerDirective = {
            restrict: 'E',
            priority: 450,
            compile: function ($element, $attrs) {
                var target = !$attrs.target || $attrs.target === 'service'
                    ? Initializer
                    : ($attrs.target === 'rootScope'
                            ? $rootScope
                            : undefined
                    );

                if (target) {
                    share(target, $element, $attrs);
                }


                return {
                    // учитывать особенность - всегда срабатывает после запуска контроллера ngController
                    pre: function ($scope, $element, $attrs) {
                        if ($attrs.target === 'scope') {
                            share($scope, $element, $attrs);
                        }
                    }
                }
            }
        };

        return InitializerDirective;

        function share(target, element, attrs) {
            if (attrs.type !== 'text/ng-init') {
                return;
            }

            var data = parseJSON(element.html());

            if (attrs.hasOwnProperty('name')) {
                if (_.isArray(data)) {
                    $parse(attrs.name).assign(target, data);
                } else if(_.isObject(data)) {
                    _.forEach(data, function(value, key) {
                        $parse(attrs.name + '.' + key).assign(target, value);
                    });
                } else {
                    $parse(attrs.name).assign(target, data);
                }
            }
            else {
                extend(target, data);
            }
        }

        function parseJSON(json) {
            return (new Function('return ' + json.replace(/\r?\n/g, ' ') + ';'))();
        }

        // deep extend
        function extend(dst) {
            angular.forEach(arguments, function (obj) {
                if (obj !== dst) {
                    angular.forEach(obj, function (value, key) {
                        if (dst[key] && dst[key].constructor && dst[key].constructor === Object) {
                            extend(dst[key], value);
                        }
                        else {
                            dst[key] = value;
                        }
                    });
                }
            });
            return dst;
        }
    }

})(angular);