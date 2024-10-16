<?php declare(strict_types=1);
/** @var \Symfony\Component\PropertyAccess\PropertyAccess $accessor */
/** @var $view */
/** @var array $keys */
/** @var array $items */
/** @var array $availableTransports */
/** @var boolean $isIncorrectTransportSelected */
/** @var string $productFieldName */

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('headerTitle', 'Multi-Product Settings');
?>

<?php if ($isIncorrectTransportSelected): ?>
  <div class="alert alert-danger" role="alert">
    To use custom API keys and transport, you must select "<b>Multiple Transport</b>"
    in the system <a class="alert-link" href="/s/config/edit?tab=emailconfig">Email settings</a>
  </div>
<?php endif; ?>

<table class="table table-hover table-striped table-bordered email-list">
  <thead>
  <tr>
      <?php
      echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
              'sessionVar' => 'email',
              'text' => utf8_ucfirst($productFieldName),
              'class' => 'col-email-name',
          ]
      );
      ?>
      <?php
      echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
              'sessionVar' => 'email',
              'text' => 'Email From',
              'class' => 'col-email-name',
          ]
      );
      ?>
      <?php
      echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
              'sessionVar' => 'email',
              'text' => 'Sender Name',
              'class' => 'col-email-name',
          ]
      );
      ?>
      <?php
      echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
              'sessionVar' => 'email',
              'text' => 'Service',
              'class' => 'col-email-name',
          ]
      );
      ?>
  </tr>
  </thead>
  <tbody style="min-height: 60vh;">
  <?php if (count($items)): ?>
      <?php foreach ($items as $key => $item): ?>
      <form
          class="form-inline"
          id="form-<?= $key ?>"
          action="<?php echo $view['router']->path('mautic_custom_email_multiproduct_save'); ?>"
          method="post">
        <input type="hidden" value="<?= $key ?>" name="product" form="form-<?= $key ?>">
      </form>
      <form
          class="form-inline"
          id="form-delete-<?= $key ?>"
          action="<?php echo $view['router']->path('mautic_custom_email_multiproduct_delete'); ?>"
          method="post">
        <input type="hidden" value="<?= $key ?>" name="product" form="form-delete-<?= $key ?>">
      </form>
      <tr>
        <td><?php echo $key; ?></td>
        <td>
          <input class="form-control" style="width: 100%;" type="email"
                 value="<?= $item['from_email'] ?>"
                 name="from_email"
                 form="form-<?= $key ?>" required>
        </td>
        <td>
          <input class="form-control" style="width: 100%;" type="text"
                 value="<?= $item['from_name'] ?>"
                 name="from_name"
                 form="form-<?= $key ?>" required>
        </td>
        <td>
          <select
              class="form-control"
              name="transport"
              id="transport-<?= $key ?>"
              form="form-<?= $key ?>">
              <?php foreach ($availableTransports as $transport => $name): ?>
                <option
                    <?php if ($item['transport'] == $transport): ?> selected <?php endif; ?>
                    value="<?php echo $transport ?>"><?php echo $name ?>
                </option>
              <?php endforeach; ?>
          </select>
        </td>
        <td>
          <div class="col-md-8">
            <input class="form-control" style="width: 100%;" type="text"
                   value="<?= $item['api_key'] ?>"
                   name="api_key"
                   form="form-<?= $key ?>" required>
          </div>
          <button type="submit" class="btn btn-primary" form="form-<?= $key ?>">Save</button>
          <button class="btn btn-danger" form="form-delete-<?= $key ?>"><i class="fa fa-times" aria-hidden="true"></i>
          </button>
        </td>
      </tr>
      <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="5"><br>There are no records yet.<br></td>
    </tr>
  <?php endif; ?>
  <tr>
    <th colspan="5"><br><h4>Add a new record:</h4></th>
  </tr>
  <form
      class="form-inline"
      id="form-add-new-record"
      action="<?php echo $view['router']->path('mautic_custom_email_multiproduct_save'); ?>"
      method="post">
  </form>
  <tr>
    <td>
      <input class="form-control" style="width: 100%;" type="text"
             name="product"
             form="form-add-new-record" required>
    </td>
    <td>
      <input class="form-control" style="width: 100%;" type="email"
             name="from_email"
             form="form-add-new-record" required>
    </td>
    <td>
      <input class="form-control" style="width: 100%;" type="text"
             name="from_name"
             form="form-add-new-record" required>
    </td>
    <td>
      <select
          class="form-control"
          name="transport"
          form="form-add-new-record">
          <?php foreach ($availableTransports as $transport => $name): ?>
            <option value="<?php echo $transport ?>"><?php echo $name ?></option>
          <?php endforeach; ?>
      </select>
    </td>
    <td>
      <div class="col-md-8">
        <input class="form-control" style="width: 100%;" type="text"
               name="api_key"
               form="form-add-new-record" required>
      </div>
      <button type="submit" class="btn btn-primary" form="form-add-new-record">Save</button>
    </td>
  </tr>
  </tbody>
</table>
