<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Dashboard</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
    <meta name="keywords"
        content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
    <meta name="author" content="CodedThemes">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('LF1.png') }}" type="image/x-icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        id="main-font-link">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/material.css') }}">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('mantis/assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('mantis/assets/css/style-preset.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    @include('layouts.sidebar')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('layouts.header')
    <!-- [ Header ] end -->



    <!-- [ Main content ] start -->
    @yield('content')

    <!-- [ Main content ] end -->
    @include('layouts.footer')
    <script src="{{ asset('mantis/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/plugins/feather.min.js') }}"></script>

    <script>
        layout_change('light');
    </script>

    <script>
        change_box_container('false');
    </script>

    <script>
        layout_rtl_change('false');
    </script>

    <script>
        preset_change("preset-1");
    </script>

    <script>
        font_change("Public-Sans");
    </script>

    <div class="offcanvas pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
        <div class="offcanvas-header bg-primary">
            <h5 class="offcanvas-title text-white">Mantis Customizer</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="pct-body" style="height: calc(100% - 60px)">
            <div class="offcanvas-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <a class="btn border-0 text-start w-100" data-bs-toggle="collapse" href="#pctcustcollapse1">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avtar avtar-xs bg-light-primary">
                                        <i class="ti ti-layout-sidebar f-18"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Theme Layout</h6>
                                    <span>Choose your layout</span>
                                </div>
                                <i class="ti ti-chevron-down"></i>
                            </div>
                        </a>
                        <div class="collapse show" id="pctcustcollapse1">
                            <div class="pct-content">
                                <div class="pc-rtl">
                                    <p class="mb-1">Direction</p>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="layoutmodertl">
                                        <label class="form-check-label" for="layoutmodertl">RTL</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <a class="btn border-0 text-start w-100" data-bs-toggle="collapse" href="#pctcustcollapse2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avtar avtar-xs bg-light-primary">
                                        <i class="ti ti-brush f-18"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Theme Mode</h6>
                                    <span>Choose light or dark mode</span>
                                </div>
                                <i class="ti ti-chevron-down"></i>
                            </div>
                        </a>
                        <div class="collapse show" id="pctcustcollapse2">
                            <div class="pct-content">
                                <div class="theme-color themepreset-color theme-layout">
                                    <a href="#!" class="active" onclick="layout_change('light')"
                                        data-value="false"><span><img src="{{ asset('mantis/assets/images/customization/default.svg') }}"
                                                alt="img"></span><span>Light</span></a>
                                    <a href="#!" class="" onclick="layout_change('dark')" data-value="true"><span><img
                                                src="{{ asset('mantis/assets/images/customization/dark.svg') }}"
                                                alt="img"></span><span>Dark</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <a class="btn border-0 text-start w-100" data-bs-toggle="collapse" href="#pctcustcollapse3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avtar avtar-xs bg-light-primary">
                                        <i class="ti ti-color-swatch f-18"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Color Scheme</h6>
                                    <span>Choose your primary theme color</span>
                                </div>
                                <i class="ti ti-chevron-down"></i>
                            </div>
                        </a>
                        <div class="collapse show" id="pctcustcollapse3">
                            <div class="pct-content">
                                <div class="theme-color preset-color">
                                    <a href="#!" class="active" data-value="preset-1"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 1</span></a>
                                    <a href="#!" class="" data-value="preset-2"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 2</span></a>
                                    <a href="#!" class="" data-value="preset-3"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 3</span></a>
                                    <a href="#!" class="" data-value="preset-4"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 4</span></a>
                                    <a href="#!" class="" data-value="preset-5"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 5</span></a>
                                    <a href="#!" class="" data-value="preset-6"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 6</span></a>
                                    <a href="#!" class="" data-value="preset-7"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 7</span></a>
                                    <a href="#!" class="" data-value="preset-8"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 8</span></a>
                                    <a href="#!" class="" data-value="preset-9"><span><img
                                                src="{{ asset('mantis/assets/images/customization/theme-color.svg') }}"
                                                alt="img"></span><span>Theme 9</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item pc-boxcontainer">
                        <a class="btn border-0 text-start w-100" data-bs-toggle="collapse" href="#pctcustcollapse4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avtar avtar-xs bg-light-primary">
                                        <i class="ti ti-border-inner f-18"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Layout Width</h6>
                                    <span>Choose fluid or container layout</span>
                                </div>
                                <i class="ti ti-chevron-down"></i>
                            </div>
                        </a>
                        <div class="collapse show" id="pctcustcollapse4">
                            <div class="pct-content">
                                <div class="theme-color themepreset-color boxwidthpreset theme-container">
                                    <a href="#!" class="active" onclick="change_box_container('false')"
                                        data-value="false"><span><img src="{{ asset('mantis/assets/images/customization/default.svg') }}"
                                                alt="img"></span><span>Fluid</span></a>
                                    <a href="#!" class="" onclick="change_box_container('true')"
                                        data-value="true"><span><img src="{{ asset('mantis/assets/images/customization/container.svg') }}"
                                                alt="img"></span><span>Container</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <a class="btn border-0 text-start w-100" data-bs-toggle="collapse" href="#pctcustcollapse5">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avtar avtar-xs bg-light-primary">
                                        <i class="ti ti-typography f-18"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Font Family</h6>
                                    <span>Choose your font family.</span>
                                </div>
                                <i class="ti ti-chevron-down"></i>
                            </div>
                        </a>
                        <div class="collapse show" id="pctcustcollapse5">
                            <div class="pct-content">
                                <div class="theme-color fontpreset-color">
                                    <a href="#!" class="active" onclick="font_change('Public-Sans')"
                                        data-value="Public-Sans"><span>Aa</span><span>Public Sans</span></a>
                                    <a href="#!" class="" onclick="font_change('Roboto')"
                                        data-value="Roboto"><span>Aa</span><span>Roboto</span></a>
                                    <a href="#!" class="" onclick="font_change('Poppins')"
                                        data-value="Poppins"><span>Aa</span><span>Poppins</span></a>
                                    <a href="#!" class="" onclick="font_change('Inter')"
                                        data-value="Inter"><span>Aa</span><span>Inter</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="collapse show">
                            <div class="pct-content">
                                <div class="d-grid">
                                    <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- [Page Specific JS] start -->
    <!-- tagify -->
    <script src="{{ asset('mantis/assets/js/plugins/choices.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        var genericExamples = document.querySelectorAll('[data-trigger]');
        for (i = 0; i < genericExamples.length; ++i) {
          var element = genericExamples[i];
          new Choices(element, {
            placeholderValue: 'This is a placeholder set in the config',
            searchPlaceholderValue: 'This is a search placeholder'
          });
        }

        var textRemove = new Choices(document.getElementById('choices-text-remove-button'), {
          delimiter: ',',
          editItems: true,
          maxItemCount: 5,
          removeItemButton: true
        });

        var text_Unique_Val = new Choices('#choices-text-unique-values', {
          paste: false,
          duplicateItemsAllowed: false,
          editItems: true
        });

        var text_i18n = new Choices('#choices-text-i18n', {
          paste: false,
          duplicateItemsAllowed: false,
          editItems: true,
          maxItemCount: 5,
          addItemText: function (value) {
            return 'Appuyez sur Entrée pour ajouter <b>"' + String(value) + '"</b>';
          },
          maxItemText: function (maxItemCount) {
            return String(maxItemCount) + 'valeurs peuvent être ajoutées';
          },
          uniqueItemText: 'Cette valeur est déjà présente'
        });

        var textEmailFilter = new Choices('#choices-text-email-filter', {
          editItems: true,
          addItemFilter: function (value) {
            if (!value) {
              return false;
            }

            const regex =
              /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            const expression = new RegExp(regex.source, 'i');
            return expression.test(value);
          }
        }).setValue(['joe@bloggs.com']);

        var textDisabled = new Choices('#choices-text-disabled', {
          addItems: false,
          removeItems: false
        }).disable();

        var textPrependAppendVal = new Choices('#choices-text-prepend-append-value', {
          prependValue: 'item-',
          appendValue: '-' + Date.now()
        }).removeActiveItems();

        var textPresetVal = new Choices('#choices-text-preset-values', {
          items: [
            'Josh Johnson',
            {
              value: 'joe@bloggs.co.uk',
              label: 'Joe Bloggs',
              customProperties: {
                description: 'Joe Blogg is such a generic name'
              }
            }
          ]
        });

        var multipleDefault = new Choices(document.getElementById('choices-multiple-groups'));

        var multipleFetch = new Choices('#choices-multiple-remote-fetch', {
          placeholder: true,
          placeholderValue: 'Pick an Strokes record',
          maxItemCount: 5
        }).setChoices(function () {
          return fetch('https://api.discogs.com/artists/55980/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW')
            .then(function (response) {
              return response.json();
            })
            .then(function (data) {
              return data.releases.map(function (release) {
                return {
                  value: release.title,
                  label: release.title
                };
              });
            });
        });

        var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
          removeItemButton: true
        });

        /* Use label on event */
        var choicesSelect = new Choices('#choices-multiple-labels', {
          removeItemButton: true,
          choices: [
            {
              value: 'One',
              label: 'Label One'
            },
            {
              value: 'Two',
              label: 'Label Two',
              disabled: true
            },
            {
              value: 'Three',
              label: 'Label Three'
            }
          ]
        }).setChoices(
          [
            {
              value: 'Four',
              label: 'Label Four',
              disabled: true
            },
            {
              value: 'Five',
              label: 'Label Five'
            },
            {
              value: 'Six',
              label: 'Label Six',
              selected: true
            }
          ],
          'value',
          'label',
          false
        );

        choicesSelect.passedElement.element.addEventListener('addItem', function (event) {
          document.getElementById('message').innerHTML =
            '<span class="badge bg-light-primary"> You just added "' + event.detail.label + '"</span>';
        });
        choicesSelect.passedElement.element.addEventListener('removeItem', function (event) {
          document.getElementById('message').innerHTML =
            '<span class="badge bg-light-danger"> You just removed "' + event.detail.label + '"</span>';
        });

        var singleFetch = new Choices('#choices-single-remote-fetch', {
          searchPlaceholderValue: 'Search for an Arctic Monkeys record'
        })
          .setChoices(function () {
            return fetch('https://api.discogs.com/artists/391170/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW')
              .then(function (response) {
                return response.json();
              })
              .then(function (data) {
                return data.releases.map(function (release) {
                  return {
                    label: release.title,
                    value: release.title
                  };
                });
              });
          })
          .then(function (instance) {
            instance.setChoiceByValue('Fake Tales Of San Francisco');
          });

        var singleXhrRemove = new Choices('#choices-single-remove-xhr', {
          removeItemButton: true,
          searchPlaceholderValue: "Search for a Smiths' record"
        }).setChoices(function (callback) {
          return fetch('https://api.discogs.com/artists/83080/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW')
            .then(function (res) {
              return res.json();
            })
            .then(function (data) {
              return data.releases.map(function (release) {
                return {
                  label: release.title,
                  value: release.title
                };
              });
            });
        });

        var singleNoSearch = new Choices('#choices-single-no-search', {
          searchEnabled: false,
          removeItemButton: true,
          choices: [
            {
              value: 'One',
              label: 'Label One'
            },
            {
              value: 'Two',
              label: 'Label Two',
              disabled: true
            },
            {
              value: 'Three',
              label: 'Label Three'
            }
          ]
        }).setChoices(
          [
            {
              value: 'Four',
              label: 'Label Four',
              disabled: true
            },
            {
              value: 'Five',
              label: 'Label Five'
            },
            {
              value: 'Six',
              label: 'Label Six',
              selected: true
            }
          ],
          'value',
          'label',
          false
        );

        var singlePresetOpts = new Choices('#choices-single-preset-options', {
          placeholder: true
        }).setChoices(
          [
            {
              label: 'Group one',
              id: 1,
              disabled: false,
              choices: [
                {
                  value: 'Child One',
                  label: 'Child One',
                  selected: true
                },
                {
                  value: 'Child Two',
                  label: 'Child Two',
                  disabled: true
                },
                {
                  value: 'Child Three',
                  label: 'Child Three'
                }
              ]
            },
            {
              label: 'Group two',
              id: 2,
              disabled: false,
              choices: [
                {
                  value: 'Child Four',
                  label: 'Child Four',
                  disabled: true
                },
                {
                  value: 'Child Five',
                  label: 'Child Five'
                },
                {
                  value: 'Child Six',
                  label: 'Child Six'
                }
              ]
            }
          ],
          'value',
          'label'
        );

        var singleSelectedOpt = new Choices('#choices-single-selected-option', {
          searchFields: ['label', 'value', 'customProperties.description'],
          choices: [
            {
              value: 'One',
              label: 'Label One',
              selected: true
            },
            {
              value: 'Two',
              label: 'Label Two',
              disabled: true
            },
            {
              value: 'Three',
              label: 'Label Three',
              customProperties: {
                description: 'This option is fantastic'
              }
            }
          ]
        }).setChoiceByValue('Two');

        var customChoicesPropertiesViaDataAttributes = new Choices('#choices-with-custom-props-via-html', {
          searchFields: ['label', 'value', 'customProperties']
        });

        var singleNoSorting = new Choices('#choices-single-no-sorting', {
          shouldSort: false
        });

        var cities = new Choices(document.getElementById('cities'));
        var tubeStations = new Choices(document.getElementById('tube-stations')).disable();

        cities.passedElement.element.addEventListener('change', function (e) {
          if (e.detail.value === 'London') {
            tubeStations.enable();
          } else {
            tubeStations.disable();
          }
        });

        var customTemplates = new Choices(document.getElementById('choices-single-custom-templates'), {
          callbackOnCreateTemplates: function (strToEl) {
            var classNames = this.config.classNames;
            var itemSelectText = this.config.itemSelectText;
            return {
              item: function (classNames, data) {
                return strToEl(
                  '\
                                <div\
                                class="' +
                    String(classNames.item) +
                    ' ' +
                    String(data.highlighted ? classNames.highlightedState : classNames.itemSelectable) +
                    '"\
                                data-item\
                                data-id="' +
                    String(data.id) +
                    '"\
                                data-value="' +
                    String(data.value) +
                    '"\
                                ' +
                    String(data.active ? 'aria-selected="true"' : '') +
                    '\
                                ' +
                    String(data.disabled ? 'aria-disabled="true"' : '') +
                    '\
                                >\
                                <span style="margin-right:10px;">🎉</span> ' +
                    String(data.label) +
                    '\
                                </div>\
                                '
                );
              },
              choice: function (classNames, data) {
                return strToEl(
                  '\
                                <div\
                                class="' +
                    String(classNames.item) +
                    ' ' +
                    String(classNames.itemChoice) +
                    ' ' +
                    String(data.disabled ? classNames.itemDisabled : classNames.itemSelectable) +
                    '"\
                                data-select-text="' +
                    String(itemSelectText) +
                    '"\
                                data-choice \
                                ' +
                    String(data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable') +
                    '\
                                data-id="' +
                    String(data.id) +
                    '"\
                                data-value="' +
                    String(data.value) +
                    '"\
                                ' +
                    String(data.groupId > 0 ? 'role="treeitem"' : 'role="option"') +
                    '\
                                >\
                                <span style="margin-right:10px;">👉🏽</span> ' +
                    String(data.label) +
                    '\
                                </div>\
                                '
                );
              }
            };
          }
        });

        var resetSimple = new Choices(document.getElementById('reset-simple'));

        var resetMultiple = new Choices('#reset-multiple', {
          removeItemButton: true
        });
      });
    </script>
    <!-- [Page Specific JS] end -->
</body>
<!-- [Body] end -->

</html>
