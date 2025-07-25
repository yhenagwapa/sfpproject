import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-bs5';

(function() {
    "use strict";

    /**
     * Easy selector helper function
     */
    const select = (el, all = false) => {
      el = el.trim()
      if (all) {
        return [...document.querySelectorAll(el)]
      } else {
        return document.querySelector(el)
      }
    }

    /**
     * Easy event listener function
     */
    const on = (type, el, listener, all = false) => {
      if (all) {
        select(el, all).forEach(e => e.addEventListener(type, listener))
      } else {
        select(el, all).addEventListener(type, listener)
      }
    }

    /**
     * Easy on scroll event listener
     */
    const onscroll = (el, listener) => {
      el.addEventListener('scroll', listener)
    }

    /**
     * Sidebar toggle
     */
    if (select('.toggle-sidebar-btn')) {
      on('click', '.toggle-sidebar-btn', function(e) {
        select('body').classList.toggle('toggle-sidebar')
      })
    }

    /**
     * Search bar toggle
     */
    if (select('.search-bar-toggle')) {
      on('click', '.search-bar-toggle', function(e) {
        select('.search-bar').classList.toggle('search-bar-show')
      })
    }

    /**
     * Navbar links active state on scroll
     */
    let navbarlinks = select('#navbar .scrollto', true)
    const navbarlinksActive = () => {
      let position = window.scrollY + 200
      navbarlinks.forEach(navbarlink => {
        if (!navbarlink.hash) return
        let section = select(navbarlink.hash)
        if (!section) return
        if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
          navbarlink.classList.add('active')
        } else {
          navbarlink.classList.remove('active')
        }
      })
    }
    window.addEventListener('load', navbarlinksActive)
    onscroll(document, navbarlinksActive)

    /**
     * Toggle .header-scrolled class to #header when page is scrolled
     */
    let selectHeader = select('#header')
    if (selectHeader) {
      const headerScrolled = () => {
        if (window.scrollY > 100) {
          selectHeader.classList.add('header-scrolled')
        } else {
          selectHeader.classList.remove('header-scrolled')
        }
      }
      window.addEventListener('load', headerScrolled)
      onscroll(document, headerScrolled)
    }

    /**
     * Back to top button
     */
    let backtotop = select('.back-to-top')
    if (backtotop) {
      const toggleBacktotop = () => {
        if (window.scrollY > 100) {
          backtotop.classList.add('active')
        } else {
          backtotop.classList.remove('active')
        }
      }
      window.addEventListener('load', toggleBacktotop)
      onscroll(document, toggleBacktotop)
    }

    /**
     * Initiate tooltips
     */
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    /**
     * Initiate quill editors
     */
    if (select('.quill-editor-default')) {
      new Quill('.quill-editor-default', {
        theme: 'snow'
      });
    }

    if (select('.quill-editor-bubble')) {
      new Quill('.quill-editor-bubble', {
        theme: 'bubble'
      });
    }

    if (select('.quill-editor-full')) {
      new Quill(".quill-editor-full", {
        modules: {
          toolbar: [
            [{
              font: []
            }, {
              size: []
            }],
            ["bold", "italic", "underline", "strike"],
            [{
                color: []
              },
              {
                background: []
              }
            ],
            [{
                script: "super"
              },
              {
                script: "sub"
              }
            ],
            [{
                list: "ordered"
              },
              {
                list: "bullet"
              },
              {
                indent: "-1"
              },
              {
                indent: "+1"
              }
            ],
            ["direction", {
              align: []
            }],
            ["link", "image", "video"],
            ["clean"]
          ]
        },
        theme: "snow"
      });
    }

    /**
     * Initiate TinyMCE Editor
     */

    const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;



    /**
     * Initiate Bootstrap validation check
     */
    var needsValidation = document.querySelectorAll('.needs-validation')

    Array.prototype.slice.call(needsValidation)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        }, false)
      })

    /**
     * Initiate Datatables
     */
    // $(document).ready(function () {
    //     $('.datatable').each(function () {
    //         if (!$.fn.DataTable.isDataTable(this)) {
    //             let isAttendanceTable = $(this).hasClass('cycle-attendance-table'); // Check if it's the attendance table

    //             $(this).DataTable({
    //                 paging: true,             // Enable paging
    //                 pageLength: 10,           // Show 10 entries per page
    //                 lengthChange: false,      // Hide the dropdown to change entry count
    //                 searching: false,
    //                 order: [[0, 'asc']],
    //                 columnDefs: [
    //                     {
    //                         orderSequence: ["desc", "asc"]
    //                     },
    //                 ],
    //                 info: false
    //             });
    //         }
    //     });
    // });


    /**
     * Autoresize echart charts
     */
    const mainContainer = select('#main');
    if (mainContainer) {
      setTimeout(() => {
        new ResizeObserver(function() {
          select('.echart', true).forEach(getEchart => {
            echarts.getInstanceByDom(getEchart).resize();
          })
        }).observe(mainContainer);
      }, 200);
    }


//     //pantawid and disability detail

//     // let additionalDetailsElement = document.getElementById('additional-details');

//     // document.addEventListener('DOMContentLoaded', function() {
//     //   // Function to toggle additional details based on radio button selection
//     //   function toggleAdditionalDetails(radioName, additionalDetailsId) {
//     //     const radios = document.getElementsByName(radioName);
//     //     const additionalDetailsSelect = document.getElementById(additionalDetailsId);

//     //     // Ensure additionalDetailsSelect exists
//     //     if (!additionalDetailsSelect) {
//     //       console.error(`Element with ID ${additionalDetailsId} not found.`);
//     //       return;
//     //     }

//     //     radios.forEach(radio => {
//     //       radio.addEventListener('change', function() {
//     //         if (radio.value === '1' && radio.checked) {
//     //           additionalDetailsSelect.disabled = false;
//     //           additionalDetailsSelect.setAttribute('required', 'required');  // Use the local variable
//     //         } else if (radio.value === '0' && radio.checked) {
//     //           additionalDetailsSelect.disabled = true;
//     //           additionalDetailsSelect.removeAttribute('required');  // Use the local variable
//     //         }
//     //       });
//     //     });
//     //   }

//     //   // Apply the function to each set of radio buttons and additional details
//     //   toggleAdditionalDetails('is_pantawid', 'pantawid_details');
//     //   toggleAdditionalDetails('is_person_with_disability', 'person_with_disability_details');
//     // });



//     // province, city and barangay


  })();
