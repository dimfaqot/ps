<!-- md -->
<?php if (user()['role'] == 'Root') : ?>
    <div class="d-none d-md-block">
        <div class="box_navbar fixed-top shadow shadow-sm">
            <div class="container d-flex justify-content-between">
                <div class="d-flex gap-1">
                    <?php
                    $db = db('menus');
                    $q1[] = ['id' => 0, 'no_urut' => 0, 'role' => user()['role'], 'menu' => 'Home', 'tabel' => 'users', 'controller' => 'home', 'icon' => "fa-solid fa-earth-asia", 'url' => 'home', 'logo' => 'file_not_found.jpg', 'group' => ''];
                    $q2 = $db->where('role', 'Root')->groupBy('group')->orderBy('urutan', 'ASC')->get()->getResultArray();
                    $menus = array_merge($q1, $q2);
                    ?>
                    <?php foreach ($menus as $m) : ?>
                        <?php if ($m['menu'] == 'Home') : ?>
                            <a href="<?= base_url($m['controller']); ?>" class="navbar_link <?= (url() == $m['controller'] ? 'navbar_active' : ''); ?> type=" button">
                                <i class="<?= $m['icon']; ?>"></i> <?= $m['menu']; ?>
                            </a>
                        <?php else : ?>
                            <div class="dropdown">
                                <a href="" class="navbar_link <?= (is_menu_active($m['group']) ? 'navbar_active' : ''); ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="<?= $m['icon']; ?>"></i> <?= $m['group']; ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach (menus() as $i) : ?>
                                        <?php if ($i['group'] == $m['group']) : ?>
                                            <li><a style="border: none;" class="dropdown-item navbar_link <?= (url() == $i['controller'] ? 'navbar_active' : ''); ?>" href="<?= base_url($i['controller']); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>

                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
                <div class="pt-1">
                    <span class="px-3 py-1" style="background-color: #f2f2f2; border:1px solid #cccccc; color:#666666;font-size:small;border-radius:10px;"><?= user()['nama']; ?>/<?= user()['role']; ?></span>
                    <a class="btn_danger" style="border-radius: 10px;" href="<?= base_url('logout'); ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> Logout</a>
                </div>
            </div>
        </div>

    </div>

<?php else : ?>
    <div class="d-none d-md-block">
        <div class="box_navbar fixed-top shadow shadow-sm">
            <div class="container d-flex justify-content-between">
                <div class="d-flex gap-1">
                    <?php foreach (menus() as $i) : ?>

                        <a href="<?= base_url($i['controller']); ?>" class="navbar_link <?= (url() == $i['controller'] ? 'navbar_active' : ''); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a>
                    <?php endforeach; ?>

                </div>
                <div class="pt-1">
                    <span class="px-3 py-1" style="background-color: #f2f2f2; border:1px solid #cccccc; color:#666666;font-size:small;border-radius:10px;"><?= user()['nama']; ?>/<?= user()['role']; ?></span>
                    <a class="btn_danger" style="border-radius: 10px;" href="<?= base_url('logout'); ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> Logout</a>
                </div>
            </div>
        </div>

    </div>

<?php endif; ?>



<!-- navbar sm -->
<div class="d-block d-md-none d-sm-block fixed-top" style="top:-5px">
    <div class="container bg-light py-2 shadow shadow-sm">
        <div class="d-flex justify-content-between">
            <div>
                <a class="navbar-brand" href="<?= base_url(); ?>"><img src="<?= base_url(); ?>logo.png" alt="LOGO" width="30"></a>
            </div>
            <div class="d-flex justify-content-center gap-1">
                <div class="pt-1">
                    <span class="px-3 py-1" style="background-color: #f2f2f2; border:1px solid #cccccc; color:#666666;font-size:x-small;border-radius:10px;"><?= user()['nama']; ?>/<?= user()['role']; ?></span>
                </div>
                <div class="pt-1">
                    <span class="px-3 py-1 bg_main text-white" style="border:1px solid #cccccc; color:#666666;font-size:x-small;border-radius:10px;"><i class="<?= menu()['icon']; ?>"></i> <?= menu()['menu']; ?></span>

                </div>

            </div>

            <div class="pt-1">
                <a href="" class="btn_act_purple" data-bs-toggle="offcanvas" data-bs-target="#leftMenu" aria-controls="leftMenu"><i class="fa-solid fa-bars text_purple"></i></a>
            </div>
        </div>

    </div>
</div>

<!-- camvas -->
<div class="offcanvas offcanvas-start" style="width:90%" data-bs-scroll="true" tabindex="-1" id="leftMenu" aria-labelledby="leftMenuLabel">
    <div class="offcanvas-header shadow shadow-bottom shadow-sm">
        <h6 class="offcanvas-title" id="leftMenuLabel">Menu</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php foreach (menus() as $i) : ?>
            <div class="mb-1 d-grid">
                <a href="<?= base_url($i['controller']); ?>" style="font-size: small;" class="px-3 py-1 <?= (url() == $i['controller'] ? 'btn_add' : 'btn_light no_underline'); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a>
            </div>
        <?php endforeach; ?>
        <div class="d-grid">
            <a class="btn_danger" href="<?= base_url('logout'); ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> Logout</a>
        </div>

    </div>
</div>