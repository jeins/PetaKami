'use strict';

angular.module('pkfrontendApp')
    .controller('AuthCtrl', AuthCtrl);

AuthCtrl.$inject = ["$auth", "$log", "$window"];
function AuthCtrl($auth, $log, $window){
	var vm = this;
	vm.init = init;
	vm.login = login;
	vm.register = register;
	vm.logout = logout;
    vm.closeAlert = closeAlert;

	init();

	function init(){
		vm.data = {};
		vm.data.email = "";
		vm.data.password = "";
		vm.data.fullName = "";

        vm.alert = [
            {type: "danger", message: "Email Already Exist!", show: false},
            {type: "danger", message: "Email / Password Salah!", show: false}
        ];
	}

	function login(){
		$auth.login({ email: vm.data.email, password: vm.data.password })
            .then(function() {
                $log.info("Success Login Manual");
                $window.location.href = '#/';
                $window.location.reload();
            })
            .catch(function(response) {
                vm.alert[1].show = true;
                $log.error(response.data.message);
            });
	}

	function register(){
		$auth.signup(vm.data)
			.then(function(){
				$log.info("Success Signup Manual");
                $window.location.href = '#/';
				$window.location.reload();
			})
			.catch(function(response){
                vm.alert[0].show = true;
				$log.error(response.data.message);
			});
	}

	function logout(){
		if(!$auth.isAuthenticated()){
			return;
		}
		$auth.logout()
			.then(function(){
                $window.location.reload();
				$log.info("Logout");
			});
	}

    function closeAlert($index){
        vm.alert[$index].show = false;
    }
}