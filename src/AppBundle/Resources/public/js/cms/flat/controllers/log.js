(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('LogCtrl', LogCtrl);

    LogCtrl.$inject = [
        '$scope',
        '$http',
        '$sce',
        '_',
        'Initializer'
    ];

    function LogCtrl(
        $scope,
        $http,
        $sce,
        _,
        Initializer
    ) {
        var countEventsElement = angular.element("#count-events"),//элемент с количеством событий
            tabEventElement = angular.element("#tab-event");//элемент таб "События"

        /**
         * загрузка логов
         */
        $http.get(Initializer.Path.AdminLogs).then(function (response) {
            $scope.logs = response.data;

            var notReadCount = 0;
            //считаем количество непрочитанных логов
            _.forEach($scope.logs, function (log) {
                if (!log.isRead) {
                    notReadCount++;
                }
            });

            if (notReadCount) {//если есть непрочитанные события
                countEventsElement.css('display', 'inline-block').text('+' + notReadCount);//показывам элемент с количеством событий
                //по клику на таб "События" отправляем запрос на прочтение новых событий
                tabEventElement.click(function ($event) {
                    $http.post(Initializer.Path.AdminReadLogs).then(function (response) {
                        if (response.data.success) {
                            countEventsElement.css('display', 'none').text('');//скрываем элемент с количеством событий
                        }
                    });
                });
            }
        });

        $scope.trustHtml = function (data) {
            return $sce.trustAsHtml(data);
        };
    }

})(angular);