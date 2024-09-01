@extends('zaco-base::layout.template')

@section('content')
<p>Warning, translations are not visible until they are exported back to the app/lang file, using
  <code>php artisan translation:export</code> command or publish button.</p>
<div class="alert alert-success success-import" style="display:none;">
  <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
</div>
<div class="alert alert-success success-find" style="display:none;">
  <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
</div>
<div class="alert alert-success success-publish" style="display:none;">
  <p>Done publishing the translations for group '<?php echo $group ?>'!</p>
</div>
<div class="alert alert-success success-publish-all" style="display:none;">
  <p>Done publishing the translations for all group!</p>
</div>
<?php if (Session::has('successPublish')): ?>
<div class="alert alert-info">
  <?php echo Session::get('successPublish'); ?>
</div>
<?php endif;?>
<p>
  <?php if (!isset($group)): ?>
<form class="form-import" method="POST"
  action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postImport') ?>"
  data-remote="true" role="form">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="form-group">
    <div class="row">
      <div class="col-sm-3">
        <select name="replace" class="form-control">
          <option value="0">Append new translations</option>
          <option value="1">Replace existing translations</option>
        </select>
      </div>
      <div class="col-sm-2">
        <button type="submit" class="btn btn-success btn-block" data-disable-with="Loading..">Import groups</button>
      </div>
    </div>
  </div>
</form>
<?php endif;?>
<?php if (isset($group)): ?>
<form class="form-inline form-publish" method="POST"
  action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postPublish', $group) ?>"
  data-remote="true" role="form"
  data-confirm="Are you sure you want to publish the translations group '<?php echo $group ?>? This will overwrite existing language files.">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <button type="submit" class="btn btn-info" data-disable-with="Publishing..">Publish translations</button>
  <a href="<?=action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@getIndex')?>"
    class="btn btn-primary">Back</a>
</form>
<?php endif;?>
</p>
<form role="form" method="POST"
  action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postAddGroup') ?>">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="form-group">
    <p>Choose a group to display the group translations. If no groups are visisble, make sure you have run the
      migrations and imported the translations.</p>
    <select name="group" id="group" class="form-control group-select">
      <?php foreach ($groups as $key => $value): ?>
      <option value="<?php echo $key ?>" <?php echo $key == $group ? ' selected' : '' ?>><?php echo $value ?></option>
      <?php endforeach;?>
    </select>
  </div>
  <div class="form-group">
    <label>Enter a new group name and start edit translations in that group</label>
    <input type="text" class="form-control" name="new-group" />
  </div>
  <div class="form-group">
    <input type="submit" class="btn btn-primary" name="add-group" value="Add and edit keys" />
  </div>
</form>
<?php if ($group): ?>
<form
  action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postAdd', array($group)) ?>"
  method="POST" role="form">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="form-group">
    <label>Add new keys to this group</label>
    <textarea class="form-control" rows="3" name="keys"
      placeholder="Add 1 key per line, without the group prefix"></textarea>
  </div>
  <div class="form-group">
    <input type="submit" value="Add keys" class="btn btn-primary">
  </div>
</form>
<hr>
<div class="card">
  <div class="card-header">
    <h4 class="card-title">Total: <?=$numTranslations?>, changed: <?=$numChanged?></h4>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-responsive-md">
        <thead>
          <tr>
            <th width="15%">Key</th>
            <?php foreach ($locales as $locale): ?>
            <th><?=$locale?></th>
            <?php endforeach;?>
            <?php if ($deleteEnabled): ?>
            <th>&nbsp;</th>
            <?php endif;?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($translations as $key => $translation): ?>
          <tr id="<?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>">
            <td><?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?></td>
            <?php foreach ($locales as $locale): ?>
            <?php $t = isset($translation[$locale]) ? $translation[$locale] : null?>

            <td>
              <a href="#edit" class="editable status-<?php echo $t ? $t->status : 0 ?> locale-<?php echo $locale ?>"
                data-locale="<?php echo $locale ?>"
                data-name="<?php echo $locale . "|" . htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>" id="username"
                data-type="textarea" data-pk="<?php echo $t ? $t->id : 0 ?>" data-url="<?php echo $editUrl ?>"
                data-title="Enter translation"><?php echo $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' ?></a>
            </td>
            <?php endforeach;?>
            <?php if ($deleteEnabled): ?>
            <td>
              <a href="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postDelete', [$group, $key]) ?>"
                class="delete-key"
                data-confirm="Are you sure you want to delete the translations for '<?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>?"><span
                  class="glyphicon glyphicon-trash"></span></a>
            </td>
            <?php endif;?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php else: ?>
<fieldset>
  <legend>Supported locales</legend>
  <p>
    Current supported locales:
  </p>
  <form class="form-remove-locale" method="POST" role="form"
    action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postRemoveLocale') ?>"
    data-confirm="Are you sure to remove this locale and all of data?">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <ul class="list-locales">
      <?php foreach ($locales as $locale): ?>
      <li>
        <div class="form-group">
          <button type="submit" name="remove-locale[<?php echo $locale ?>]" class="btn btn-danger btn-xs"
            data-disable-with="...">
            &times;
          </button>
          <?php echo $locale ?>

        </div>
      </li>
      <?php endforeach;?>
    </ul>
  </form>
  <form class="form-add-locale" method="POST" role="form"
    action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postAddLocale') ?>">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <p>
        Enter new locale key:
      </p>
      <div class="row">
        <div class="col-sm-3">
          <input type="text" name="new-locale" class="form-control" />
        </div>
        <div class="col-sm-2">
          <button type="submit" class="btn btn-primary btn-block" data-disable-with="Adding..">Add new locale</button>
        </div>
      </div>
    </div>
  </form>
</fieldset>
<fieldset>
  <legend>Export all translations</legend>
  <form class="form-inline form-publish-all" method="POST"
    action="<?php echo action('\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@postPublish', '*') ?>"
    data-remote="true" role="form"
    data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <button type="submit" class="btn btn-primary" data-disable-with="Publishing..">Publish all</button>
  </form>
</fieldset>

<?php endif;?>
@endsection

@push('style')
<link href="/themes/admin/css/styles/bootstrap-editable.css" rel="stylesheet" />
<link href="/themes/admin/css/styles/dark-editable.css" rel="stylesheet" />
@endpush
@push('script')

<script src="/themes/admin/js/scripts/translation/tether.js" type="text/javascript"></script>
<script src="/themes/admin/js/scripts/translation/bootstrap-editable.js" type="text/javascript"></script>
<script src="/themes/admin/js/scripts/translation/form-xeditable.init.js" type="text/javascript"></script>
<script src="/themes/admin/js/scripts/translation/dark-editable.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
<script>
jQuery(document).ready(function($) {
  console.log('2022-04-17 08:59:54---');
  $.ajaxSetup({
    beforeSend: function(xhr, settings) {
      console.log('beforesend');
      settings.data += "&_token=<?php echo csrf_token() ?>";
    }
  });

  $('.editable').editable().on('hidden', function(e, reason) {
    var locale = $(this).data('locale');
    if (reason === 'save') {
      $(this).removeClass('status-0').addClass('status-1');
    }
    if (reason === 'save' || reason === 'nochange') {
      var $next = $(this).closest('tr').next().find('.editable.locale-' + locale);
      setTimeout(function() {
        $next.editable('show');
      }, 300);
    }
  });

  $('.group-select').on('change', function() {
    var group = $(this).val();
    if (group) {
      window.location.href =
        '<?php echo action('\\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@getView') ?>/' + $(
          this).val();
    } else {
      window.location.href =
        '<?php echo action('\\ZacoSoft\ZacoBase\Http\Controllers\Admin\TranslationController@getIndex') ?>';
    }
  });

  $("a.delete-key").on('confirm:complete', function(event, result) {
    if (result) {
      var row = $(this).closest('tr');
      var url = $(this).attr('href');
      var id = row.attr('id');
      $.post(url, {
        id: id
      }, function() {
        row.remove();
      });
    }
    return false;
  });

  $('.form-import').on('ajax:success', function(e, data) {
    $('div.success-import strong.counter').text(data.counter);
    $('div.success-import').slideDown();
    window.location.reload();
  });

  $('.form-find').on('ajax:success', function(e, data) {
    $('div.success-find strong.counter').text(data.counter);
    $('div.success-find').slideDown();
    window.location.reload();
  });


  $('.form-publish').on('ajax:success', function(e, data) {
    $('div.success-publish').slideDown();
  });

  $('.form-publish-all').on('ajax:success', function(e, data) {
    $('div.success-publish-all').slideDown();
  });
  $('.enable-auto-translate-group').click(function(event) {
    event.preventDefault();
    $('.autotranslate-block-group').removeClass('hidden');
    $('.enable-auto-translate-group').addClass('hidden');
  })
  $('#base-locale').change(function(event) {
    console.log($(this).val());
    $.cookie('base_locale', $(this).val());
  })
  if (typeof $.cookie('base_locale') !== 'undefined') {
    $('#base-locale').val($.cookie('base_locale'));
  }
})
</script>
@endpush
