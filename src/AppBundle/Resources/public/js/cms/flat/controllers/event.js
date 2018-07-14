(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('EventCtrl', EventCtrl);

    EventCtrl.$inject = [
        '$scope',
        '$http',
        '$window',
        'FormHelper',
        'Initializer'
    ];

    function EventCtrl(
        $scope,
        $http,
        $window,
        FormHelper,
        Initializer
    ) {
        /**
         * данные формы
         * @type {{}}
         */
        $scope.formData = {};
        
        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            var requestConfigs = {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'//отправляем заголовок, чтобы на бэекенде определить isXmlHttpRequest
                }
            };

            $http.post('/app_dev.php' + Initializer.Path.SubmitEvent, $scope.formData, requestConfigs)
                .then(function (response) {
                    if (response.data.success) {
                        //если все ок - перезагружем странцу, чтобы подтянуть новый лог
                        $window.location.reload();
                    } else {
                        //если не success - выводим ошибки с бэкенда
                        FormHelper.showBackendErrors(response.data.errors, form);
                    }
                });
        }
    }

})(angular);