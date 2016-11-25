import manager from '#/main/core/_old/resource/manager/manager'
import $ from 'jquery'

/* global FileAPI */
/* eslint no-unused-vars: ["error", { "vars": "local" }] */

$(document).on('shown.bs.modal', '.modal', function() {
  $(this).find('[autofocus]').focus()
})

manager.createFullManager(window.managerConfig)
window.FileAPI = { debug: false, pingUrl: false }