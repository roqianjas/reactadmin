<!-- css -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>


<!-- js -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">



<!-- Control -->
<script>
    $(document).ready(function() {
        function show_data() {
            $.ajax({
                url: '<?= site_url('admin/menu') ?>',
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    var menu = ''
                    for (var i = 0; i < data.length; i++) {
                        var sub = '';
                        for (var j = 0; j < data[i].submenu.length; j++) {
                            submenu = '<li class="nav-item" data-url="' + data[i].submenu[j].url + '">' +
                                '<a href="#" class="nav-link">' +
                                '<i class="' + data[i].submenu[j].icon + ' nav-icon"></i>' +
                                '<p>' + data[i].submenu[j].title + '</p>' +
                                '</a>' +
                                '</li>';
                            sub += submenu;
                        }
                        menu += '<li class="nav-item has-treeview">' +
                            '<a href="#" class="nav-link">' +
                            '<i class="nav-icon ' + data[i].icon + '"></i>' +
                            '<p>' +
                            data[i].title +
                            '<i class="right fas fa-angle-left"></i>' +
                            '</p>' +
                            '</a>' +
                            '<ul class="nav nav-treeview submenu" >' + sub + '</ul>' +
                            '</li>';
                    }
                    $('#menu').html(menu);
                    $('.nav-link').click(function() {
                        $('.nav-link').removeClass('active');
                        $(this).addClass('active');
                    });
                    $('.submenu').on('click', '.nav-item', function() {
                        url = $(this).data('url');
                        $('#show_data').load('<?= site_url() ?>' + '/' + url);
                    });
                }
            })
        }
        const form = $('.modal-body').html();
        id_menu = $('#id_menu').val();
        $('#myData').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('submenu/getLists/'); ?>" + id_menu,
                "type": "POST"
            },
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }]
        });
        $('#tambah').click(function() {
            $('.modal-body').html(form);
            aksi =
                '<input type="hidden" name="aksi" id="aksi">' +
                '<input type="hidden" name="id_m" id="id_m">';
            $('#add').html(aksi);
            $('#modal').find('h5').html('Tambah')
            $('#modal').find('#btn').html('Tambah')
            $('#aksi').val('tambah');
            $('#id_m').val(id_menu);
            $('#modal').modal('show');
        });
        $('#data').on('click', '.edit', function() {
            $('.modal-body').html(form);
            aksi = '<input type="hidden" name="aksi" id="aksi">' +
                '<input type="hidden" name="id" id="id">' +
                '<input type="hidden" name="id_m" id="id_m">';
            $('#add').html(aksi);
            $('#modal').find('h5').html('Edit')
            $('#modal').find('#btn').html('Edit')
            id = $(this).data('id_submenu');
            title = $(this).data('title');
            icon = $(this).data('icon');
            url = $(this).data('url');
            $('#aksi').val('edit');
            $('#id').val(id);
            $('#title').val(title);
            $('#icon').val(icon);
            $('#url').val(url);
            $('#id_m').val(id_menu);
            $('#modal').modal('show');
        });
        $('#data').on('click', '.hapus', function() {
            $('.modal-body').html(form);
            aksi = '<input type="hidden" name="aksi" id="aksi">' +
                '<input type="hidden" name="id" id="id">' +
                '<h3>Apakah Anda Yakin ?</h3>';
            $('.modal-body').html(aksi);
            $('#modal').find('h5').html('Hapus')
            $('#modal').find('#btn').html('Hapus')
            id = $(this).data('id_submenu');
            $('#aksi').val('hapus');
            $('#id').val(id);
            $('#modal').modal('show');
        });
        $('#form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= site_url('submenu/aksi') ?>',
                type: 'post',
                data: new FormData(this),
                processData: false,
                contentType: false,
                async: false,
                success: function(data) {
                    show_data();
                    var pesan = data;
                    $(document).Toasts('create', {
                        title: 'Success',
                        body: pesan,
                        class: 'bg-success mt-4 mr-4'
                    });
                    $('#myData').DataTable().ajax.reload();
                    $('#modal').modal('hide');
                }
            })
        });
        $('#data').on('click', '#active', function() {
            id_menu = $(this).data('id_submenu');
            active = $(this).data('active');
            $.ajax({
                url: '<?= site_url('submenu/active') ?>',
                type: 'post',
                data: {
                    id: id_menu,
                    active: active
                },
                dataType: 'json',
                success: function(data) {
                    show_data();
                    $('#myData').DataTable().ajax.reload();
                    if (data.active == 'true') {
                        $(document).Toasts('create', {
                            title: 'Success',
                            body: 'Submenu Aktif',
                            class: 'bg-success mt-4 mr-4'
                        });
                    } else {
                        $(document).Toasts('create', {
                            title: 'Success',
                            body: 'Submenu Nonaktif',
                            class: 'bg-danger mt-4 mr-4'
                        });
                    }
                }
            })
        });
        $('#kembali').click(function() {
            $('#show_data').load('<?= site_url('menu') ?>');
        });
        $('#reload').click(function() {
            $('#show_data').load('<?= site_url('submenu/index/') ?>' + id_menu);
        });

        $('#data').on('click', '.down', function() {
            no = $(this).data('order');
            id = $(this).data('id_menu');
            id_sub = $(this).data('id_submenu');
            $.ajax({
                url: '<?= site_url('submenu/down') ?>',
                type: 'post',
                data: {
                    no_order: no,
                    id_menu: id,
                    id_submenu: id_sub
                },
                dataType: 'json',
                success: function(result) {
                    show_data();
                    $('#myData').DataTable().ajax.reload();
                }
            })
        });
        $('#data').on('click', '.up', function() {
            no = $(this).data('order');
            id = $(this).data('id_menu');
            id_sub = $(this).data('id_submenu');
            $.ajax({
                url: '<?= site_url('submenu/up') ?>',
                type: 'post',
                data: {
                    no_order: no,
                    id_menu: id,
                    id_submenu: id_sub
                },
                dataType: 'json',
                success: function(result) {
                    show_data();
                    $('#myData').DataTable().ajax.reload();
                }
            })
        });

    });
</script>