(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('FlatCtrl', FlatCtrl);

    FlatCtrl.$inject = [
        '$scope',
        '$http',
        '$q',
        '$window',
        'Initializer',
        'FormHelper'
    ];

    function FlatCtrl(
        $scope,
        $http,
        $q,
        $window,
        Initializer,
        FormHelper
    ) {
        /**
         * форма
         * @type {{}}
         */
        $scope.form = {};

        var queries = [
            $http.get(Initializer.Path.FlatTypes),
            $http.get(Initializer.Path.FlatHouses)
        ];

        if (Initializer.Settings.FlatId) {
            queries.push($http.get(Initializer.Path.Flat));
        }

        /**
         * загрузка типов помещений, домов и информации о помещении
         */
        $q.all(queries)
            .then(function (response) {
                $scope.state.blocks.flat.types = response[0].data;
                $scope.state.blocks.flat.houses = response[1].data;

                if (Initializer.Settings.FlatId) {
                    $scope.state.blocks.flat.model = response[2].data;

                    //чекбоксы в сонате не работают через ng-model - приходится делать через iCheck и эвент ifChanged
                    //устанавливаем или снимаем чекбокс "Архивный"
                    angular.element('#archive').iCheck($scope.state.blocks.flat.model.archive ? 'check' : 'uncheck');
                    /**
                     * отслеживаем изменение чекбокса "Архивный"
                     */
                    angular.element('#archive').on('ifChanged', function (event) {
                        $scope.state.blocks.flat.model.archive = event.target.checked;
                    });
                }
            }, function (error) {
                alert('Во время загрузки данных произошла ошибка. Попробуйте обновить страницу.');
            })
            .finally(function () {
                $scope.state.loading = false;
            });

        /**
         * сохранение помещения
         * @param $event
         * @param form
         */
        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.invalid) {
                return;
            }

            $scope.state.loading = true;

            var flat = $scope.state.blocks.flat.model;
            var submitData = {
                id: Initializer.Settings.FlatId,
                number: flat.number,
                archive: flat.archive,
                house: flat.house.id,
                type: flat.type.id
            };

            $http.post(Initializer.Path.FlatSubmit, submitData)
                .then(function (response) {
                    if (response.data.success) {
                        if (Initializer.Settings.FlatId) {
                            $scope.state.blocks.flat.model = response.data.flat;
                            $scope.state.blocks.flat.isUpdated = true;
                        } else {
                            $window.location = Initializer.Path.FlatEdit.replace('0', response.data.flat.id);
                        }
                    } else {
                        FormHelper.showBackendErrors(response.data.errors, form);
                    }
                }, function (error) {
                    alert('Во время сохранения данных произошла ошибка. Попробуйте заново заполнить форму и повторить сохранение.')
                })
                .finally(function () {
                    $scope.state.loading = false;
                });
        }
    }
})(angular);
