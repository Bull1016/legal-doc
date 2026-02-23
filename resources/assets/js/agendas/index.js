/**
 * App Calendar
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const direction = isRtl ? 'rtl' : 'ltr';

  (function () {
    const calendarEl = document.getElementById('calendar');
    const appCalendarSidebar = document.querySelector('.app-calendar-sidebar');
    const addEventSidebar = document.getElementById('addEventSidebar');
    const appOverlay = document.querySelector('.app-overlay');
    const offcanvasTitle = document.querySelector('.offcanvas-title');
    const btnToggleSidebar = document.querySelector('.btn-toggle-sidebar');
    const btnSubmit = document.getElementById('addEventBtn');

    const eventTitle = document.getElementById('title');
    const eventStartDate = document.getElementById('begin_at');
    const eventEndDate = document.getElementById('end_at');
    const eventLabel = $('#type');
    const eventLocation = document.getElementById('location');
    const eventDescription = document.getElementById('description');
    const selectAll = document.querySelector('.select-all');
    const filterInput = [].slice.call(document.querySelectorAll('.input-filter'));
    const inlineCalendar = document.querySelector('.inline-calendar');

    // Calendar settings
    const calendarsColor = {
      project: 'warning',
      activity: 'success'
    };

    let eventToUpdate,
      currentEvents = [],
      isFormValid = false,
      inlineCalInstance,
      start,
      end;

    // Offcanvas Instance
    const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);

    // --------------------------------------------------------------------------------------------------
    // AXIOS: Fetch Events
    // --------------------------------------------------------------------------------------------------
    function fetchEvents(info, successCallback) {
      notyf.open({
        type: 'info',
        message: window.translations.retriving_events
      });

      // Collect checked filters
      const filters = [];
      filterInput.forEach(item => {
        if (item.checked) {
          filters.push(item.getAttribute('data-value'));
        }
      });

      const categories = filters.length ? filters : null;

      if (!categories) {
        successCallback([]);
        return;
      }

      axios
        .get('/agendas/events', {
          params: {
            types: categories
          }
        })
        .then(response => {
          currentEvents = response.data;
          successCallback(currentEvents);
        })
        .catch(error => {
          notyf.error(error.response.data.message);
          console.error(error);
          successCallback([]);
        });
    }

    // Initialize Select2
    if (eventLabel.length) {
      function renderBadges(option) {
        if (!option.id) return option.text;
        return (
          "<span class='badge badge-dot bg-" + $(option.element).data('label') + " me-2'> " + '</span>' + option.text
        );
      }
      eventLabel.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: eventLabel.parent(),
        templateResult: renderBadges,
        templateSelection: renderBadges,
        minimumResultsForSearch: -1,
        escapeMarkup: function (es) {
          return es;
        }
      });
    }

    // Init Flatpickr
    if (eventStartDate) {
      start = eventStartDate.flatpickr({
        enableTime: false,
        altInput: true,
        altFormat: 'Y-m-d',
        dateFormat: 'Y-m-d',
        monthSelectorType: 'static',
        onReady: function (selectedDates, dateStr, instance) {
          if (instance.isMobile) instance.mobileInput.setAttribute('step', null);
        }
      });
    }

    if (eventEndDate) {
      end = eventEndDate.flatpickr({
        enableTime: false,
        altInput: true,
        altFormat: 'Y-m-d',
        dateFormat: 'Y-m-d',
        monthSelectorType: 'static',
        onReady: function (selectedDates, dateStr, instance) {
          if (instance.isMobile) instance.mobileInput.setAttribute('step', null);
        }
      });
    }

    // Inline sidebar calendar
    if (inlineCalendar) {
      inlineCalInstance = inlineCalendar.flatpickr({
        monthSelectorType: 'static',
        static: true,
        inline: true,
        onChange: function (date) {
          calendar.changeView(calendar.view.type, moment(date[0]).format('YYYY-MM-DD'));
          modifyToggler();
          appCalendarSidebar.classList.remove('show');
          appOverlay.classList.remove('show');
        }
      });
    }

    // Init FullCalendar
    let calendar = new Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: fetchEvents,
      plugins: [dayGridPlugin, interactionPlugin, listPlugin, timegridPlugin],
      editable: true,
      dragScroll: true,
      dayMaxEvents: 2,
      eventResizableFromStart: true,
      customButtons: {
        sidebarToggle: { text: 'Sidebar' }
      },
      headerToolbar: {
        start: 'sidebarToggle, prev,next, title',
        end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      direction: direction,
      initialDate: new Date(),
      navLinks: true,
      eventClassNames: function ({ event: calendarEvent }) {
        const type = calendarEvent.extendedProps.type;
        const colorName = calendarsColor[type] || 'primary';
        return ['bg-label-' + colorName];
      },
      dateClick: function (info) {
        const date = moment(info.date).format('YYYY-MM-DD');
        resetValues();
        bsAddEventSidebar.show();

        if (offcanvasTitle) offcanvasTitle.innerHTML = __('Add Event');
        btnSubmit.innerHTML = window.translations.submit;
        btnSubmit.classList.remove('btn-update-event');
        btnSubmit.classList.add('btn-add-event');

        eventStartDate.value = date;
        eventEndDate.value = date;
        // Update flatpickr
        start.setDate(date);
        end.setDate(date);
      },
      eventClick: function (info) {
        eventClick(info);
      },
      datesSet: function () {
        modifyToggler();
      },
      viewDidMount: function () {
        modifyToggler();
      }
    });

    calendar.render();
    modifyToggler();

    function modifyToggler() {
      const fcSidebarToggleButton = document.querySelector('.fc-sidebarToggle-button');
      if (fcSidebarToggleButton) {
        fcSidebarToggleButton.classList.remove('fc-button-primary');
        fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
        while (fcSidebarToggleButton.firstChild) fcSidebarToggleButton.firstChild.remove();
        fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
        fcSidebarToggleButton.setAttribute('data-overlay', '');
        fcSidebarToggleButton.setAttribute('data-target', '#app-calendar-sidebar');
        fcSidebarToggleButton.insertAdjacentHTML(
          'beforeend',
          '<i class="icon-base ti tabler-menu-2 icon-lg text-heading"></i>'
        );
      }
    }

    // Filters
    if (selectAll) {
      selectAll.addEventListener('click', e => {
        if (e.currentTarget.checked) {
          document.querySelectorAll('.input-filter').forEach(c => (c.checked = true));
        } else {
          document.querySelectorAll('.input-filter').forEach(c => (c.checked = false));
        }
        calendar.refetchEvents();
      });
    }

    if (filterInput) {
      filterInput.forEach(item => {
        item.addEventListener('click', () => {
          document.querySelectorAll('.input-filter:checked').length < document.querySelectorAll('.input-filter').length
            ? (selectAll.checked = false)
            : (selectAll.checked = true);
          calendar.refetchEvents();
        });
      });
    }

    // Toggle Sidebar
    if (btnToggleSidebar) {
      btnToggleSidebar.addEventListener('click', e => {
        btnSubmit.innerHTML = window.translations.submit;
        btnSubmit.classList.remove('btn-update-event');
        btnSubmit.classList.add('btn-add-event');
        resetValues();
        bsAddEventSidebar.show();
      });
    }

    // Handle Edit Event Click
    function eventClick(info) {
      eventToUpdate = info.event;
      if (eventToUpdate.url) {
        info.jsEvent.preventDefault();
        window.open(eventToUpdate.url, '_blank');
      }
      bsAddEventSidebar.show();

      if (offcanvasTitle) offcanvasTitle.innerHTML = window.translations.update;
      btnSubmit.innerHTML = window.translations.update;
      btnSubmit.classList.add('btn-update-event');
      btnSubmit.classList.remove('btn-add-event');

      eventTitle.value = eventToUpdate.title;
      // start/end are Date objects in fullCalendar
      let startD = moment(eventToUpdate.start).format('YYYY-MM-DD');
      let endD = eventToUpdate.end ? moment(eventToUpdate.end).format('YYYY-MM-DD') : startD;

      eventStartDate.value = startD;
      eventEndDate.value = endD;

      start.setDate(startD); // Flatpickr
      end.setDate(endD); // Flatpickr

      $(eventLabel).val(eventToUpdate.extendedProps.type).trigger('change');

      eventLocation.value = eventToUpdate.extendedProps.location || '';
      eventDescription.value = eventToUpdate.extendedProps.description || '';

      // Banner handling
      const bannerPreview = document.getElementById('banner-preview');
      const bannerImg = bannerPreview.querySelector('img');
      if (eventToUpdate.extendedProps.banner) {
        bannerImg.src = '/storage/' + eventToUpdate.extendedProps.banner;
        bannerPreview.style.display = 'block';
      } else {
        bannerPreview.style.display = 'none';
      }
    }

    // Banner Preview handling
    const bannerInput = document.getElementById('banner');
    if (bannerInput) {
      bannerInput.addEventListener('change', function () {
        const file = this.files[0];
        const bannerPreview = document.getElementById('banner-preview');
        const bannerImg = bannerPreview.querySelector('img');
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            bannerImg.src = e.target.result;
            bannerPreview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      });
    }

    // Form Validation and Submission
    const eventForm = document.getElementById('eventForm');
    const fv = FormValidation.formValidation(eventForm, {
      fields: {
        title: { validators: { notEmpty: { message: window.translations.event_title_required } } },
        begin_at: { validators: { notEmpty: { message: window.translations.event_start_date_required } } },
        end_at: { validators: { notEmpty: { message: window.translations.event_end_date_required } } },
        type: { validators: { notEmpty: { message: window.translations.event_type_required } } }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass: '', rowSelector: '.mb-5' }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    })
      .on('core.form.valid', function () {
        isFormValid = true;
      })
      .on('core.form.invalid', function () {
        isFormValid = false;
      });

    btnSubmit.addEventListener('click', e => {
      fv.validate().then(function (status) {
        if (status === 'Valid') {
          if (btnSubmit.classList.contains('btn-add-event')) {
            addEvent();
          } else {
            updateEvent();
          }
        }
      });
    });

    function addEvent() {
      const formData = new FormData(eventForm);
      axios
        .post('/agendas', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(response => {
          notyf.success(response.data.message);
          calendar.refetchEvents();
          bsAddEventSidebar.hide();
        })
        .catch(error => {
          notyf.error(error.response.data.message || 'Error occurred');
          console.error(error);
        });
    }

    function updateEvent() {
      const id = eventToUpdate.id;
      const formData = new FormData(eventForm);
      formData.append('_method', 'PUT');

      axios
        .post('/agendas/' + id, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(response => {
          notyf.success(response.data.message);
          calendar.refetchEvents();
          bsAddEventSidebar.hide();
        })
        .catch(error => {
          notyf.error(error.response.data.message || 'Error occurred');
          console.error(error);
        });
    }

    function resetValues() {
      eventEndDate.value = '';
      eventStartDate.value = '';
      if (start) start.clear(); // Clear flatpickr
      if (end) end.clear(); // Clear flatpickr
      eventTitle.value = '';
      eventLocation.value = '';
      $(eventLabel).val('activity').trigger('change'); // Default to activity
      eventDescription.value = '';

      const bannerInput = document.getElementById('banner');
      if (bannerInput) bannerInput.value = '';
      const bannerPreview = document.getElementById('banner-preview');
      if (bannerPreview) bannerPreview.style.display = 'none';
    }
  })();
});
