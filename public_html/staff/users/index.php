<?php
use App\Classes\User;
use App\Classes\Pagination;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('/index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id']) && isset($_GET['cmd'])) {
  $id = $_GET['id'] ?? 0;
  $cmd = $_GET['cmd'] ?? 0;

  if ($id && $cmd) {
    if ($cmd === 'delete') {
      $user = User::findById($id);
  
      if ($user->delete() === true) {
        $session->message("The user '" . $user->username . "' was deleted.");
        redirect_to(url_for('/staff/users/index.php'));
      }
    }
  }
}

$current_page = $_GET['page'] ?? 1;
$per_page = DASHBOARD_PER_PAGE;
$total_count = User::countAll();
$pagination = new Pagination($current_page, $per_page, $total_count);

$users = User::queryUsersWithPostsNum($per_page, $pagination->offset());

$page_title = 'Users';
include SHARED_PATH . '/staff_header.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content adminContentJS">

      <h2 class="text-center mb-1">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
      </h2>

      <?php if (empty($users)): ?>
        <p class="lead">There is no user yet.</p>
      
      <?php else: ?>
        <?php echo display_session_message('msg success') ?>

        <table class="table table-striped table-bordered table-hover table-light table-md">
          <thead class="bg-muted-lk text-muted">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Username</th>
              <th scope="col">Email</th>
              <th scope="col">Type</th>
              <th scope="col">Since</th>
              <th scope="col">Posted</th>
              <th scope="colgroup" colspan="2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($users as $key => $user): ?>
              <tr data-user="<?php echo $user->id ?>">
                <th scope="row"><?php echo $key + 1 ?></th>
                <td><a href="#"><?php echo $user->username ?></a></td>
                <td><a href="mailto: <?php echo $user->email ?>" class="<?php echo ($user->email_confirmed ? 'text-success' : '') ?>"><?php echo $user->email ?></a></td>
                <td><span class="text-primary"><?php echo $user->user_type ?></span></td>
                <td><span class="h5"><?php echo date('M j, Y', strtotime($user->created_at)) ?></span></td>
                <td><?php echo $user->posted ?> - <span class="text-success font-weight-bold">
                  <?php echo $user->approved ?></span>
                </td>
                <td scope="colgroup" colspan="1">
                  <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/users/edit.php?id=' . $user->id) ?>">
                    Edit
                  </a>
                </td>
                <td scope="colgroup" colspan="1">
                  <?php $data = no_gaps_between("
                    table-users,
                    id-{$user->id},
                    username-{$user->username}
                  ") ?>
                  <a data-delete="<?php echo $data ?>" class="btn-lk btn-lk--danger"
                    href="<?php echo url_for('staff/delete.php?table=users&id=' . $user->id)
                  ?>">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php
          $url = url_for('staff/users/index.php');
          echo $pagination->pageLinks($url);
        ?>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>