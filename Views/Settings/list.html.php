<?php declare(strict_types=1);
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('headerTitle', 'Email API Keys');
?>

<?php if (count($items)): ?>

    <table class="table table-hover table-striped table-bordered email-list">
      <thead>
      <tr>
          <?php
          echo $view->render(
              'MauticCoreBundle:Helper:tableheader.html.php',
              [
                  'sessionVar' => 'email',
                  'text' => 'mautic.core.id',
                  'class' => 'visible-md visible-lg col-email-id',
              ]
          );
          ?>
          <?php
          echo $view->render(
              'MauticCoreBundle:Helper:tableheader.html.php',
              [
                  'sessionVar' => 'email',
                  'text' => 'mautic.core.name',
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

      <?php foreach ($items as $item): ?>
        <tr>
          <form class="form-inline"
                action="<?php echo $view['router']->path('mautic_custom_email_settings_set_key'); ?>" method="post">
            <input type="hidden" value="<?= $item->getId() ?>" name="email_id">
            <td><?php echo $item->getId(); ?></td>
            <td><?php echo $item->getName(); ?></td>
            <td><?php echo $item->getFromAddress(); ?></td>
            <td><?php echo $item->getFromName(); ?></td>
            <td>
              <select class="form-control" name="custom_transport" id="custom_transport">
                <option value="<?= $defaultTransport ?>">Not selected</option>
                <option
                    <?php if ($keys[$item->getId()]['transport'] == "mautic.transport.sparkpost"): ?> selected <?php endif; ?>
                    value="mautic.transport.sparkpost">Sparkpost
                </option>
                <option
                    <?php if ($keys[$item->getId()]['transport'] == "mautic.transport.sendgrid_api"): ?> selected <?php endif; ?>
                    value="mautic.transport.sendgrid_api">SendGrid - API
                </option>
              </select>
            </td>
            <td>
              <div class="col-md-8">
                <input class="form-control" style="width: 100%;" type="text"
                       value="<?= isset($keys[$item->getId()]) ? $keys[$item->getId()]['key'] : '' ?>"
                       name="custom_api_key"
                       placeholder="API key - if not specified, will be used default">
              </div>
              <button type="submit" class="btn btn-primary">Confirm</button>
            </td>
          </form>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

<?php endif; ?>
