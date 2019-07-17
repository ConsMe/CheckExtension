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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/ChangeAdmin.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/ChangeAdmin.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  props: ['choosedadmin', 'checkers'],
  data: function data() {
    return {
      admin: JSON.parse(JSON.stringify(this.choosedadmin)),
      newpassword: '',
      symbols: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
      disabled: {
        newpassword: false
      }
    };
  },
  computed: {
    hasCheckers: function hasCheckers() {
      if (!this.admin.checkers) return [];
      return this.admin.checkers.map(function (checker) {
        return checker.user.id;
      });
    },
    list: function list() {
      var _this = this;

      return this.checkers.filter(function (checker) {
        return !_this.hasCheckers.includes(checker.id);
      });
    },
    sortedCheckers: function sortedCheckers() {
      if (!this.admin.checkers) return [];
      return this.admin.checkers.sort(function (checker1, checker2) {
        return -(checker1.user.name < checker2.user.name) || +(checker1.user.name > checker2.user.name);
      });
    }
  },
  created: function created() {
    console.log(this.admin);
  },
  methods: {
    addChecker: function addChecker(id, name) {
      var _this2 = this;

      Vue.set(this.checkers.filter(function (checker) {
        return checker.id == id;
      })[0], 'disabled', true);
      axios.post("/admins/addchecker", {
        checker_id: id,
        admin_id: this.admin.id
      }).then(function (response) {
        if (_this2.admin.checkers) {
          _this2.admin.checkers.push({
            user: {
              id: id,
              name: name
            }
          });
        } else {
          Vue.set(_this2.admin, 'checkers', [{
            user: {
              id: id,
              name: name
            }
          }]);
        }

        toastr.success("\u0427\u0435\u043A\u0435\u0440 ".concat(name, " \u0434\u043E\u0431\u0430\u0432\u043B\u0435\u043D"));

        _this2.$emit('refreshdata', response.data);
      })["catch"](function (error) {
        if (error.response && error.response.data.errors) {
          toastr.warning(Object.entries(error.response.data.errors)[0][1][0]);
        }

        toastr.warning('Что-то пошло не так, попробуйте позднее');
      })["finally"](function () {
        _this2.checkers.filter(function (checker) {
          return checker.id == id;
        })[0].disabled = false;
      });
    },
    removeChecker: function removeChecker(id, name) {
      var _this3 = this;

      Vue.set(this.checkers.filter(function (checker) {
        return checker.id == id;
      })[0], 'disabled', true);
      axios.post("/admins/removechecker", {
        checker_id: id,
        admin_id: this.admin.id
      }).then(function (response) {
        _this3.admin.checkers.some(function (checker, i) {
          if (checker.user.id == id) {
            _this3.admin.checkers.splice(i, 1);

            return true;
          }
        });

        toastr.success("\u0427\u0435\u043A\u0435\u0440 ".concat(name, " \u0443\u0434\u0430\u043B\u0435\u043D"));

        _this3.$emit('refreshdata', response.data);
      })["catch"](function (error) {
        if (error.response && error.response.data.errors) {
          toastr.warning(Object.entries(error.response.data.errors)[0][1][0]);
        } else {
          toastr.warning('Что-то пошло не так, попробуйте позднее');
        }
      })["finally"](function () {
        _this3.checkers.filter(function (checker) {
          return checker.id == id;
        })[0].disabled = false;
      });
    },
    passgen: function passgen() {
      this.newpassword = chance.string({
        length: 15,
        pool: this.symbols
      });
    },
    changePassword: function changePassword() {
      var _this4 = this;

      this.disabled.newpassword = true;
      var password = this.newpassword;
      axios.post("/users/changepassword", {
        id: this.admin.id,
        password: password
      }).then(function (response) {
        toastr.success("\u041F\u0430\u0440\u043E\u043B\u044C \u0447\u0435\u043A\u0435\u0440\u0430 ".concat(name, " \u0438\u0437\u043C\u0435\u043D\u0435\u043D"));
      })["catch"](function (error) {
        if (error.response && error.response.data.errors) {
          toastr.warning(Object.entries(error.response.data.errors)[0][1][0]);
        } else {
          toastr.warning('Что-то пошло не так, попробуйте позднее');
        }
      })["finally"](function () {
        _this4.disabled.newpassword = false;
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/ChangeAdmin.vue?vue&type=template&id=47721e38&":
/*!**************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/ChangeAdmin.vue?vue&type=template&id=47721e38& ***!
  \**************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "modal-content" }, [
    _c("div", { staticClass: "modal-header" }, [
      _c("h4", { staticClass: "modal-title" }, [
        _vm._v(_vm._s("Админ " + _vm.admin.name))
      ]),
      _vm._v(" "),
      _vm._m(0)
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "modal-body" }, [
      _c("h5", [_vm._v("Доступ к чекерам")]),
      _vm._v(" "),
      _c("div", { staticClass: "row" }, [
        _c("div", { staticClass: "col" }, [
          _c("div", { staticClass: "card border-primary mb-3 h-100" }, [
            _c("div", { staticClass: "card-header text-left" }, [
              _vm._v("Чекеры")
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "card-body text-left" },
              _vm._l(_vm.list, function(checker) {
                return _c(
                  "button",
                  {
                    key: checker.id,
                    staticClass: "btn btn-outline-info btn-sm mr-2 mb-2",
                    attrs: { type: "button", disabled: checker.disabled },
                    on: {
                      click: function($event) {
                        return _vm.addChecker(checker.id, checker.name)
                      }
                    }
                  },
                  [
                    _vm._v(
                      "\n                            " +
                        _vm._s(checker.name) +
                        "\n                        "
                    )
                  ]
                )
              }),
              0
            )
          ])
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "col" }, [
          _c("div", { staticClass: "card border-primary mb-3 h-100" }, [
            _c("div", { staticClass: "card-header text-left" }, [
              _vm._v("Разрешен доступ")
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "card-body text-left" },
              _vm._l(_vm.sortedCheckers, function(checker) {
                return _c(
                  "button",
                  {
                    key: checker.user.id,
                    staticClass: "btn btn-outline-info btn-sm mr-2 mb-2",
                    attrs: { type: "button" },
                    on: {
                      click: function($event) {
                        return _vm.removeChecker(
                          checker.user.id,
                          checker.user.name
                        )
                      }
                    }
                  },
                  [
                    _vm._v(
                      "\n                            " +
                        _vm._s(checker.user.name) +
                        "\n                        "
                    )
                  ]
                )
              }),
              0
            )
          ])
        ])
      ]),
      _vm._v(" "),
      _c(
        "form",
        {
          staticClass: "form-inline justify-content-center mt-5",
          on: {
            submit: function($event) {
              $event.preventDefault()
              return _vm.changePassword($event)
            }
          }
        },
        [
          _c("label", { staticClass: "mr-2" }, [_vm._v("Изменить пароль")]),
          _vm._v(" "),
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.newpassword,
                expression: "newpassword"
              }
            ],
            staticClass: "form-control mr-2",
            attrs: {
              type: "text",
              placeholder: "Новый пароль",
              autocomplete: "off",
              disabled: _vm.disabled.newpassword,
              required: ""
            },
            domProps: { value: _vm.newpassword },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.newpassword = $event.target.value
              }
            }
          }),
          _vm._v(" "),
          _c(
            "button",
            {
              staticClass: "btn btn-primary mr-2",
              attrs: { type: "submit", disabled: _vm.disabled.newpassword }
            },
            [_vm._v("Изменить")]
          ),
          _vm._v(" "),
          _c(
            "button",
            {
              staticClass: "btn btn-secondary",
              attrs: { type: "button", disabled: _vm.disabled.newpassword },
              on: {
                click: function($event) {
                  return _vm.passgen()
                }
              }
            },
            [_vm._v("PassGen")]
          ),
          _vm._v(" "),
          _vm._m(1)
        ]
      )
    ]),
    _vm._v(" "),
    _vm._m(2)
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c(
      "button",
      {
        staticClass: "close",
        attrs: {
          type: "button",
          "data-dismiss": "modal",
          "aria-label": "Close"
        }
      },
      [_c("span", { attrs: { "aria-hidden": "true" } }, [_vm._v("×")])]
    )
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "text-center text-secondary" }, [
      _c("small", [_vm._v("Админ будет автоматически разлогинен")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "modal-footer" }, [
      _c(
        "button",
        {
          staticClass: "btn btn-light",
          attrs: { type: "button", "data-dismiss": "modal" }
        },
        [_vm._v("Закрыть")]
      )
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return normalizeComponent; });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ }),

/***/ "./resources/js/admin.js":
/*!*******************************!*\
  !*** ./resources/js/admin.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

Vue.component('change-admin', __webpack_require__(/*! ./components/ChangeAdmin.vue */ "./resources/js/components/ChangeAdmin.vue")["default"]);
new Vue({
  el: '#app',
  data: {
    symbols: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    name: '',
    password: '',
    disabled: {
      add: false,
      "delete": false
    },
    errors: {
      name: false,
      password: false
    },
    admins: window.admins,
    deleteAdminId: null,
    choosedAdmin: null,
    checkers: window.checkers
  },
  computed: {
    sortedAdmins: function sortedAdmins() {
      return this.admins.sort(function (admin1, admin2) {
        return -(admin1.name < admin2.name) || +(admin1.name > admin2.name);
      });
    }
  },
  mounted: function mounted() {
    var _this = this;

    $(this.$refs.confirmation).on('hidden.bs.modal', function (e) {
      _this.deleteAdminId = null;
    });
    $(this.$refs.changeadminwindow).on('hidden.bs.modal', function (e) {
      _this.choosedAdmin = null;
    });
  },
  methods: {
    passgen: function passgen() {
      this.password = chance.string({
        length: 15,
        pool: this.symbols
      });
    },
    addadmin: function addadmin() {
      var _this2 = this;

      this.disabled.add = true;
      var name = this.name;
      axios.post("/register/admin", {
        name: name,
        password: this.password
      }).then(function (response) {
        _this2.name = '';
        _this2.password = '';
        toastr.success("\u0410\u0434\u043C\u0438\u043D ".concat(name, " \u0434\u043E\u0431\u0430\u0432\u043B\u0435\u043D"));

        _this2.refreshData(response.data);
      })["catch"](function (error) {
        if (error.response.data && error.response.data.errors) {
          toastr.warning(Object.entries(error.response.data.errors)[0][1][0]);
          return;
        }

        toastr.warning('Что-то пошло не так, попробуйте позднее');
      })["finally"](function () {
        _this2.disabled.add = false;
      });
    },
    confirmDeleteAdmin: function confirmDeleteAdmin(id) {
      this.deleteAdminId = id;
      $(this.$refs.confirmation).modal('show');
    },
    deleteAdmin: function deleteAdmin() {
      var _this3 = this;

      if (!this.deleteAdminId) return;
      this.disabled["delete"] = true;
      var id = this.deleteAdminId;
      $(this.$refs.confirmation).modal('hide');
      var name = this.admins.filter(function (admin) {
        return admin.id == id;
      })[0].name;
      axios.post("/admins/delete", {
        id: id
      }).then(function (response) {
        toastr.success("\u0410\u0434\u043C\u0438\u043D ".concat(name, " \u0443\u0434\u0430\u043B\u0435\u043D"));

        _this3.refreshData(response.data);
      })["catch"](function (error) {
        toastr.warning('Что-то пошло не так, попробуйте позднее');
      })["finally"](function () {
        _this3.disabled["delete"] = false;
      });
    },
    changeAdminWindow: function changeAdminWindow(id) {
      this.choosedAdmin = this.admins.filter(function (admin) {
        return admin.id == id;
      })[0];
      $(this.$refs.changeadminwindow).modal('show');
    },
    refreshData: function refreshData(data) {
      this.admins = data.admin ? data.admin : [];
      this.checkers = data.checker ? data.checker : [];
    }
  }
});

/***/ }),

/***/ "./resources/js/components/ChangeAdmin.vue":
/*!*************************************************!*\
  !*** ./resources/js/components/ChangeAdmin.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ChangeAdmin_vue_vue_type_template_id_47721e38___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ChangeAdmin.vue?vue&type=template&id=47721e38& */ "./resources/js/components/ChangeAdmin.vue?vue&type=template&id=47721e38&");
/* harmony import */ var _ChangeAdmin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ChangeAdmin.vue?vue&type=script&lang=js& */ "./resources/js/components/ChangeAdmin.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ChangeAdmin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ChangeAdmin_vue_vue_type_template_id_47721e38___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ChangeAdmin_vue_vue_type_template_id_47721e38___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/ChangeAdmin.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/ChangeAdmin.vue?vue&type=script&lang=js&":
/*!**************************************************************************!*\
  !*** ./resources/js/components/ChangeAdmin.vue?vue&type=script&lang=js& ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ChangeAdmin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./ChangeAdmin.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/ChangeAdmin.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ChangeAdmin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/ChangeAdmin.vue?vue&type=template&id=47721e38&":
/*!********************************************************************************!*\
  !*** ./resources/js/components/ChangeAdmin.vue?vue&type=template&id=47721e38& ***!
  \********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ChangeAdmin_vue_vue_type_template_id_47721e38___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./ChangeAdmin.vue?vue&type=template&id=47721e38& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/ChangeAdmin.vue?vue&type=template&id=47721e38&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ChangeAdmin_vue_vue_type_template_id_47721e38___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ChangeAdmin_vue_vue_type_template_id_47721e38___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ 0:
/*!*************************************!*\
  !*** multi ./resources/js/admin.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/alex/Documents/FL/CheckExtension/CheckExtension/resources/js/admin.js */"./resources/js/admin.js");


/***/ })

/******/ });