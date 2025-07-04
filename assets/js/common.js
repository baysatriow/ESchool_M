// Common JavaScript functions for ESchool_M
$(document).ready(() => {
  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Initialize popovers
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  var popoverList = popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

  // DataTable default configuration
  if ($.fn.DataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json",
      },
      pageLength: 25,
      responsive: true,
      autoWidth: false,
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
      pagingType: "full_numbers",
    })
  }

  // Form validation helper
  window.validateForm = (formId) => {
    const form = document.getElementById(formId)
    if (form) {
      form.classList.add("was-validated")
      return form.checkValidity()
    }
    return false
  }

  // Loading button helper
  window.setLoadingButton = (buttonId, loading = true) => {
    const $btn = $("#" + buttonId)
    const $btnText = $btn.find(".btn-text")
    const $btnLoading = $btn.find(".btn-loading")

    if (loading) {
      $btn.prop("disabled", true)
      $btnText.addClass("d-none")
      $btnLoading.removeClass("d-none")
    } else {
      $btn.prop("disabled", false)
      $btnText.removeClass("d-none")
      $btnLoading.addClass("d-none")
    }
  }

  // Number formatting helper
  window.formatNumber = (num) => new Intl.NumberFormat("id-ID").format(num)

  // Currency formatting helper
  window.formatCurrency = (num) => "Rp " + new Intl.NumberFormat("id-ID").format(num)

  // Date formatting helper
  window.formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("id-ID", {
      year: "numeric",
      month: "long",
      day: "numeric",
    })
  }

  // Confirm delete helper
  window.confirmDelete = (message = "Apakah Anda yakin ingin menghapus data ini?") => confirm(message)

  // Show toast notification
  window.showToast = (type, title, message) => {
    if (typeof iziToast !== "undefined") {
      iziToast[type]({
        title: title,
        message: message,
        position: "topRight",
        timeout: 5000,
      })
    } else {
      alert(title + ": " + message)
    }
  }
})
