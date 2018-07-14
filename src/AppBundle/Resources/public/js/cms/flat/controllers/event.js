(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('EventCtrl', EventCtrl);

    EventCtrl.$inject = [
        '$scope',
        '$http',
        'FormHelper',
        'Initializer'
    ];

    function EventCtrl(
        $scope,
        $http,
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

            $http.post('/app_dev.php' + Initializer.Path.SubmitEvent, $scope.formData)
                .then(function (response) {
                    console.log(response.data);
                });
        }
    }

})(angular);