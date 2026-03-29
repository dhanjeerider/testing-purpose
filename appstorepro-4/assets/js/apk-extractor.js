/* APK Extractor — admin JS */
(function ($) {
  'use strict';

  var i18n = ApkExtractor.i18n;
  var ajaxUrl = ApkExtractor.ajaxUrl;
  var nonce = ApkExtractor.nonce;

  var currentAppData = null;

  /* ── Tab Navigation ── */
  $(document).on('click', '.apk-tab', function () {
    var tab = $(this).data('tab');
    $('.apk-tab').removeClass('active').attr('aria-selected', 'false');
    $(this).addClass('active').attr('aria-selected', 'true');
    $('.apk-tab-panel').removeClass('active').hide().prop('hidden', true);
    $('#tab-' + tab).addClass('active').show().prop('hidden', false);
  });

  /* ── Single Scrape ── */
  $('#apk-scrape-btn, #apk-rescrape-btn').on('click', function () {
    var url = $('#apk-single-url').val().trim();
    if (!isValidPlayStoreUrl(url)) {
      showStatus('#apk-single-status', 'error', i18n.invalid_url);
      return;
    }
    doScrape(url);
  });

  function doScrape(url) {
    currentAppData = null;
    $('#apk-single-result').hide();
    showStatus('#apk-single-status', 'loading', '<span class="apk-spinner"></span> ' + i18n.scraping);

    $.post(ajaxUrl, {
      action: 'appstorepro_scrape_playstore',
      nonce: nonce,
      url: url
    })
    .done(function (res) {
      if (res.success && res.data) {
        currentAppData = res.data;
        renderResult(res.data);
        showStatus('#apk-single-status', 'success', '&#10003; ' + i18n.done);
        $('#apk-single-result').show();
      } else {
        var msg = (res.data && res.data.message) ? res.data.message : i18n.error;
        showStatus('#apk-single-status', 'error', '&#10007; ' + msg);
      }
    })
    .fail(function () {
      showStatus('#apk-single-status', 'error', '&#10007; ' + i18n.error + ': Network error');
    });
  }

  function renderResult(app) {
    /* Icon */
    var iconHtml = '';
    if (app.icon) {
      iconHtml = '<img class="apk-fetched-icon" src="' + escHtml(app.icon) + '" alt="' + escHtml(app.title) + '">';
    } else {
      var initial = app.title ? app.title.charAt(0).toUpperCase() : '?';
      iconHtml = '<div class="apk-icon-placeholder">' + escHtml(initial) + '</div>';
    }
    $('#apk-result-icon-wrap').html(iconHtml);
    // Handle broken icon gracefully
    $('#apk-result-icon-wrap img').on('error', function () {
      var initial2 = app.title ? app.title.charAt(0).toUpperCase() : '?';
      $(this).replaceWith('<div class="apk-icon-placeholder">' + escHtml(initial2) + '</div>');
    });

    $('#apk-result-title').text(app.title || '—');
    $('#apk-result-developer').text(app.developer || '');

    /* Pills */
    var pills = '';
    if (app.rating) pills += '<span class="apk-pill">&#9733; ' + escHtml(app.rating) + '</span>';
    if (app.category) pills += '<span class="apk-pill">' + escHtml(app.category) + '</span>';
    if (app.size) pills += '<span class="apk-pill">' + escHtml(app.size) + '</span>';
    if (app.downloads) pills += '<span class="apk-pill">' + escHtml(app.downloads) + '</span>';
    if (app.android_version) pills += '<span class="apk-pill">Android ' + escHtml(app.android_version) + '+</span>';
    $('#apk-result-pills').html(pills);

    /* Data Rows */
    var rows = '';
    var fields = [
      { label: 'Package ID', key: 'package' },
      { label: 'Version', key: 'version' },
      { label: 'Size', key: 'size' },
      { label: 'Downloads', key: 'downloads' },
      { label: 'Category', key: 'category' },
      { label: 'Rating', key: 'rating' },
      { label: 'Android Version', key: 'android_version' },
      { label: 'Download URL', key: 'download_url' },
      { label: 'Telegram URL', key: 'telegram_url' },
      { label: 'Telegram Members', key: 'telegram_members' },
      { label: 'YouTube URL', key: 'youtube_url' },
      { label: 'MOD Info', key: 'mod_info' },
      { label: 'Play Store URL', key: 'play_store_url' },
    ];

    fields.forEach(function (f) {
      var val = app[f.key];
      if (!val) return;
      var urlFields = ['play_store_url', 'download_url', 'telegram_url', 'youtube_url'];
      if (urlFields.indexOf(f.key) !== -1) {
        val = '<a href="' + escHtml(val) + '" target="_blank" rel="noopener">' + escHtml(val) + '</a>';
      } else {
        val = escHtml(val);
      }
      rows += '<tr><th>' + escHtml(f.label) + '</th><td>' + val + '</td></tr>';
    });

    /* Icon row */
    if (app.icon) {
      rows += '<tr><th>Icon</th><td class="apk-icon-cell"><img class="apk-data-icon" src="' + escHtml(app.icon) + '" alt=""></td></tr>';
    }

    /* Screenshots */
    if (app.screenshots && app.screenshots.length) {
      var sc = '';
      app.screenshots.forEach(function (imgUrl) {
        var thumb = $('<img>').attr({
          src: imgUrl,
          alt: '',
          'class': 'apk-screenshot-thumb'
        }).css('border-radius', '4px');
        thumb.on('error', function () { $(this).hide(); });
        sc += '<img class="apk-screenshot-thumb" src="' + escHtml(imgUrl) + '" alt="" style="margin-right:4px;vertical-align:middle;">';
      });
      rows += '<tr><th>Screenshots (' + app.screenshots.length + ')</th><td>' + sc + '</td></tr>';
    }

    /* Description */
    if (app.description) {
      // Strip HTML tags safely before displaying as text
      var tmpDiv = document.createElement('div');
      tmpDiv.innerHTML = app.description;
      var plainDesc = (tmpDiv.textContent || tmpDiv.innerText || '').substring(0, 300);
      rows += '<tr><th>Description</th><td>' + escHtml(plainDesc) + '&hellip;</td></tr>';
    }

    $('#apk-data-rows').html(rows);
    // Attach error handlers to images via jQuery instead of inline onerror
    $('#apk-data-rows img').on('error', function () { $(this).hide(); });
    $('#apk-single-result .apk-screenshot-thumb').on('error', function () { $(this).hide(); });
  }

  /* ── Create App Post ── */
  $('#apk-create-btn').on('click', function () {
    if (!currentAppData) {
      showStatus('#apk-create-status', 'error', i18n.error + ': No data scraped yet.');
      return;
    }
    if (!currentAppData.title) {
      showStatus('#apk-create-status', 'error', i18n.no_title);
      return;
    }

    showStatus('#apk-create-status', 'loading', '<span class="apk-spinner"></span> ' + i18n.creating);
    $('#apk-create-btn').prop('disabled', true);
    var targetType = $('#apk-target-type').val() || 'app';

    $.post(ajaxUrl, {
      action: 'appstorepro_create_app_post',
      nonce: nonce,
      app_data: JSON.stringify(currentAppData),
      skip_existing: '1',
      post_type: targetType
    })
    .done(function (res) {
      $('#apk-create-btn').prop('disabled', false);
      if (res.success && res.data) {
        var d = res.data;
        var links = '';
        if (d.edit_url) links += ' <a href="' + escHtml(d.edit_url) + '" target="_blank">' + i18n.edit_post + '</a>';
        if (d.view_url) links += ' · <a href="' + escHtml(d.view_url) + '" target="_blank">' + i18n.view_post + '</a>';
        var msg = d.skipped
          ? '&#10003; Skipped (already exists): <strong>' + escHtml(d.title) + '</strong>' + links
          : '&#10003; ' + i18n.created + ': <strong>' + escHtml(d.title) + '</strong>' + links;
        showStatus('#apk-create-status', 'success', msg);
      } else {
        var errMsg = (res.data && res.data.message) ? res.data.message : i18n.error;
        showStatus('#apk-create-status', 'error', '&#10007; ' + errMsg);
      }
    })
    .fail(function () {
      $('#apk-create-btn').prop('disabled', false);
      showStatus('#apk-create-status', 'error', '&#10007; ' + i18n.error + ': Network error');
    });
  });

  /* ── Bulk Import ── */
  $('#apk-bulk-btn').on('click', function () {
    var rawText = $('#apk-bulk-urls').val().trim();
    if (!rawText) return;

    var lines = rawText.split('\n').map(function (l) { return l.trim(); }).filter(isValidPlayStoreUrl);
    if (!lines.length) {
      alert(i18n.invalid_url);
      return;
    }

    var skipExisting = $('#apk-bulk-skip-existing').is(':checked');
    $('#apk-bulk-btn').prop('disabled', true);
    $('#apk-bulk-results').show();
    $('#apk-bulk-results-body').empty();
    $('#apk-bulk-progress-wrap').show();
      updateProgress(0, lines.length);

    // Build rows
    lines.forEach(function (url, idx) {
      var rowId = 'bulk-row-' + idx;
      $('#apk-bulk-results-body').append(
        '<tr id="' + rowId + '">' +
        '<td>' + (idx + 1) + '</td>' +
        '<td class="apk-bulk-row-title"><small>' + escHtml(url) + '</small></td>' +
        '<td><span class="apk-bulk-status pending">&#8212; Pending</span></td>' +
        '<td class="apk-bulk-actions"></td>' +
        '</tr>'
      );
    });

    // Sequential processing
    var completed = 0;
    var targetType = $('#apk-target-type').val() || 'app';
    function processNext(index) {
      if (index >= lines.length) {
        updateProgress(lines.length, lines.length);
        showBulkProgressLabel(i18n.bulk_done);
        $('#apk-bulk-btn').prop('disabled', false);
        return;
      }
      var url = lines[index];
      var rowId = '#bulk-row-' + index;
      $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status processing"><span class="apk-spinner"></span> ' + i18n.bulk_progress + '…</span>');

      $.post(ajaxUrl, { action: 'appstorepro_scrape_playstore', nonce: nonce, url: url })
        .done(function (res) {
          if (res.success && res.data && res.data.title) {
            var appData = res.data;
            var iconHtml = appData.icon
              ? '<img src="' + escHtml(appData.icon) + '" class="apk-bulk-row-icon">'
              : '';
            var $iconImg = $(iconHtml);
            $iconImg.on('error', function () { $(this).hide(); });
            $(rowId + ' .apk-bulk-row-title').html('')
              .append($iconImg)
              .append($('<strong>').text(appData.title));
            // Now create post
            $.post(ajaxUrl, {
              action: 'appstorepro_create_app_post',
              nonce: nonce,
              app_data: JSON.stringify(appData),
              skip_existing: skipExisting ? '1' : '0',
              post_type: targetType
            })
            .done(function (cres) {
              completed++;
              updateProgress(completed, lines.length);
              if (cres.success && cres.data) {
                var d = cres.data;
                if (d.skipped) {
                  $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status skipped">&#10007; Skipped</span>');
                } else {
                  $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status done">&#10003; Created</span>');
                }
                var links = '';
                if (d.edit_url) links += '<a href="' + escHtml(d.edit_url) + '" target="_blank">' + i18n.edit_post + '</a>';
                if (d.view_url) links += '<a href="' + escHtml(d.view_url) + '" target="_blank">' + i18n.view_post + '</a>';
                $(rowId + ' .apk-bulk-actions').html(links);
              } else {
                $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status error">&#10007; Create failed</span>');
              }
              processNext(index + 1);
            })
            .fail(function () {
              completed++;
              updateProgress(completed, lines.length);
              $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status error">&#10007; Network error</span>');
              processNext(index + 1);
            });
          } else {
            completed++;
            updateProgress(completed, lines.length);
            var errMsg = (res.data && res.data.message) ? res.data.message : 'Scrape failed';
            $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status error">&#10007; ' + escHtml(errMsg) + '</span>');
            processNext(index + 1);
          }
        })
        .fail(function () {
          completed++;
          updateProgress(completed, lines.length);
          $(rowId + ' td:nth-child(3)').html('<span class="apk-bulk-status error">&#10007; Network error</span>');
          processNext(index + 1);
        });
    }

    processNext(0);
  });

  /* ── Helpers ── */
  function isValidPlayStoreUrl(url) {
    if (typeof url !== 'string') return false;
    // Accept any valid HTTP(S) URL. The server-side scraper will intelligently
    // extract metadata from any website using JSON-LD, Open Graph, and other
    // common metadata sources.
    return /^https?:\/\/.+\..+/i.test(url);
  }

  function showStatus(selector, type, html) {
    var $el = $(selector);
    $el.removeClass('loading success error').addClass(type).html(html).show();
  }

  function updateProgress(done, total) {
    var pct = total > 0 ? Math.round((done / total) * 100) : 0;
    $('#apk-bulk-progress-bar').css('width', pct + '%');
    showBulkProgressLabel(i18n.bulk_progress + ' ' + done + ' / ' + total + ' (' + pct + '%)');
  }

  function showBulkProgressLabel(msg) {
    $('#apk-bulk-progress-label').text(msg);
  }

  function escHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

})(jQuery);
