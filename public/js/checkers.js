/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/checkers.js":
/*!**********************************!*\
  !*** ./resources/js/checkers.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

new Vue({
  el: '#app',
  data: {
    symbols: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    name: '',
    password: '',
    newpassword: '',
    disabled: {
      add: false,
      "delete": false,
      changepassword: false
    },
    errors: {
      name: false,
      password: false
    },
    checkers: window.checkers,
    deleteCheckerId: null,
    changePasswordId: null
  },
  mounted: function mounted() {
    var _this = this;

    $(this.$refs.confirmation).on('hidden.bs.modal', function (e) {
      _this.deleteCheckerId = null;
    });
    $(this.$refs.changeData).on('hidden.bs.modal', function (e) {
      _this.changePasswordId = null;
      _this.newpassword = '';
    });
  },
  computed: {
    sortedCheckers: function sortedCheckers() {
      if (this.checkers.length <= 1) return this.checkers;
      return this.checkers.sort(function (checker1, checker2) {
        return -(checker1.name < checker2.name) || +(checker1.name > checker2.name);
      });
    },
    deleteCheckerName: function deleteCheckerName() {
      var _this2 = this;

      if (this.deleteCheckerId) {
        return this.checkers.filter(function (checker) {
          return checker.id == _this2.deleteCheckerId;
        })[0].name;
      }

      return '';
    },
    changePasswordName: function changePasswordName() {
      var _this3 = this;

      if (this.changePasswordId) {
        return this.checkers.filter(function (checker) {
          return checker.id == _this3.changePasswordId;
        })[0].name;
      }

      return '';
    },
    chekerUrls: function chekerUrls() {
      return this.sortedCheckers.map(function (checker) {
        if ('checkertasks' in checker) {
          return checker.checkertasks.filter(function (task) {
            return task.isworking;
          });
        }

        return '';
      });
    }
  },
  methods: {
    passgen: function passgen(password) {
      this[password] = chance.string({
        length: 15,
        pool: this.symbols
      });
    },
    addChecker: function addChecker() {
      var _this4 = this;

      this.disabled.add = true;
      var name = this.name;
      axios.post("/register/checker", {
        name: name,
        password: this.password
      }).then(function (response) {
        _this4.name = '';
        _this4.password = '';
        toastr.success("\u0427\u0435\u043A\u0435\u0440 ".concat(name, " \u0434\u043E\u0431\u0430\u0432\u043B\u0435\u043D"));
        _this4.checkers = response.data;
      })["catch"](function (error) {
        if (error.response.data.errors) {
          toastr.warning(Object.entries(error.response.data.errors)[0][1][0]);
          return;
        }

        toastr.warning('Что-то пошло не так, попробуйте позднее');
      })["finally"](function () {
        _this4.disabled.add = false;
      });
    },
    confirmDeleteChecker: function confirmDeleteChecker(id) {
      this.deleteCheckerId = id;
      $(this.$refs.confirmation).modal('show');
    },
    deleteChecker: function deleteChecker() {
      var _this5 = this;

      if (!this.deleteCheckerId) return;
      this.disabled["delete"] = true;
      var id = this.deleteCheckerId;
      $(this.$refs.confirmation).modal('hide');
      var name = this.checkers.filter(function (checker) {
        return checker.id == id;
      })[0].name;
      axios.post("/checkers/delete", {
        id: id
      }).then(function (response) {
        toastr.success("\u0427\u0435\u043A\u0435\u0440 ".concat(name, " \u0443\u0434\u0430\u043B\u0435\u043D"));
        _this5.checkers = response.data;
      })["catch"](function (error) {
        toastr.warning('Что-то пошло не так, попробуйте позднее');
      })["finally"](function () {
        _this5.disabled["delete"] = false;
      });
    },
    showChangeWindow: function showChangeWindow(id) {
      this.changePasswordId = id;
      $(this.$refs.changeData).modal('show');
    },
    changePassword: function changePassword() {
      var _this6 = this;

      if (!this.changePasswordId) return;
      var id = this.changePasswordId;
      var password = this.newpassword;
      var name = this.checkers.filter(function (checker) {
        return checker.id == id;
      })[0].name;
      this.disabled.changepassword = true;
      axios.post("/users/changepassword", {
        id: id,
        password: password
      }).then(function (response) {
        toastr.success("\u041F\u0430\u0440\u043E\u043B\u044C \u0447\u0435\u043A\u0435\u0440\u0430 ".concat(name, " \u0438\u0437\u043C\u0435\u043D\u0435\u043D"));
        $(_this6.$refs.changeData).modal('hide');
      })["catch"](function (error) {
        if (error.response.data.errors) {
          toastr.warning(Object.entries(error.response.data.errors)[0][1][0]);
          return;
        }

        toastr.warning('Что-то пошло не так, попробуйте позднее');
      })["finally"](function () {
        _this6.disabled.changepassword = false;
      });
      ;
    }
  }
});

/***/ }),

/***/ 1:
/*!****************************************!*\
  !*** multi ./resources/js/checkers.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/alex/Documents/FL/CheckExtension/CheckExtension/resources/js/checkers.js */"./resources/js/checkers.js");


/***/ })

/******/ });