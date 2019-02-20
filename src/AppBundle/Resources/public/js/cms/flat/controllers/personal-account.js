(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('PersonalAccountCtrl', PersonalAccountCtrl);

    PersonalAccountCtrl.$inject = [
        '$scope',
        '$http',
        'Initializer'
    ];

    function PersonalAccountCtrl(
        $scope,
        $http,
        Initializer
    ) {
        /**
         * форма
         * @type {{}}
         */
        $scope.form = {};

        $scope.$watch('state.blocks.personalAccount.toggle', function (newValue) {
            if (!newValue) {
                return;
            }

            $http.get(Initializer.Path.PersonalAccountList, {cache: true}).then(function (response) {
                console.log(response);
            });
        });
    }
})(angular);
