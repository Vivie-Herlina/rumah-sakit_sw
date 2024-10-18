@extends("Layout.Admin_layout")
@section("admin")
<style>
    td,
    th {
        text-align: center;
    }
</style>
<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card" style="width: max-content;">
                <div class="card-body">
                    <h5 class="card-title">Data Apoteker di {{ Auth::user()->nama_lengkap }}</h5>
                    <button type="button" class="btn btn-primary mt-4 mb-4" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Tambah Apoteker
                    </button>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Apoteker</th>
                                    <th>Email</th>
                                    <th>No Hp</th>
                                    <th>Alamat</th>
                                    <th>No SIPA</th>
                                    <th>Golongan Darah</th>
                                    <th>Umur</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Foto</th>
                                    <th>Status Apoteker</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @if($tenaga_apoteker != null)
                                @foreach($tenaga_apoteker as $apoteker)
                                <tr>
                                    <td>{{ $apoteker->user->nama_lengkap }}</td>
                                    <td>{{ $apoteker->user->email }}</td>
                                    <td>{{ $apoteker->user->No_hp }}</td>
                                    <td>{{ $apoteker->alamat_lengkap }}</td>
                                    <td>{{ $apoteker->no_SIPA }}</td>
                                    <td>{{ $apoteker->Gol_darah }}</td>
                                    <td>{{ $apoteker->umur }}</td>
                                    <td>{{ $apoteker->user->tanggal_lahir }}</td>
                                    <td><img src="{{ asset('asset/img/userfoto/' . $apoteker->user->foto) }}" alt="Foto Apoteker" style="width: 50px; height: 50px;"></td>
                                    <td>{{ $apoteker->Status_apoteker }}</td>
                                    <td>
                                        <button class="btn btn-warning edit-btn" type="button" data-id="{{ $apoteker->user->id }}">Edit</button>
                                        <button class="btn btn-danger delete-btn" type="button" data-id="{{ $apoteker->user->id }}">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <form id="apotekerForm" action="/createapoteker" method="post" class="d-flex p-4 flex-column" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="apoteker_id">
                            <input type="hidden" name="id_rumah_sakit" value="{{ $rumahsakit }}">

                            <label for="nama_lengkap" class="form-label">Nama Lengkap Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                                <input type="text" class="form-control" placeholder="Nama Lengkap Apoteker" name="nama_lengkap" id="nama_lengkap" required>
                            </div>

                            <label for="nik" class="form-label">No NIK Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" class="form-control" placeholder="NIK Apoteker" name="nik" id="nik" required>
                            </div>

                            <label for="email" class="form-label">Email untuk akun Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-envelope-heart-fill"></i></span>
                                <input type="email" class="form-control" placeholder="Email Apoteker" name="email" id="email">
                            </div>

                            <label for="No_hp" class="form-label">No.Hp Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" placeholder="No.hp Apoteker" name="No_hp" id="No_hp">
                            </div>

                            <label for="alamat_lengkap" class="form-label">Alamat Lengkap Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                <textarea class="form-control" placeholder="Alamat Lengkap Apoteker" name="alamat_lengkap" id="alamat_lengkap"></textarea>
                            </div>

                            <label for="no_SIPA" class="form-label">Nomor SIPA Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><img src="/asset/img/sip.svg" width="20px" alt=""></span>
                                <input type="text" class="form-control" placeholder="No SIPA" name="no_SIPA" id="no_SIPA">
                            </div>

                            <label for="Gol_darah" class="form-label">Golongan Darah Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><img src="/asset/img/blood.svg" width="20px" alt=""></span>
                                <input type="text" class="form-control" placeholder="Golongan Darah Apoteker" name="Gol_darah" id="Gol_darah">
                            </div>

                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-calendar-heart"></i></span>
                                <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir">
                            </div>

                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                <select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required>
                                    <option selected>Pilih Jenis Kelamin</option>
                                    <option value="1">Laki-laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>

                            <label for="password" class="form-label">Password Untuk akun Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" placeholder="Password" name="password" id="password" required>
                                <span class="input-group-text"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>

                            <label for="umur" class="form-label">Umur Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-calendar-heart"></i></span>
                                <input type="number" class="form-control" placeholder="Umur Apoteker" name="umur" id="umur">
                            </div>

                            <label for="Status_apoteker" class="form-label">Status Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                <select class="form-select" name="Status_apoteker" id="Status_apoteker" required>
                                    <option selected>Pilih Status Apoteker</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak aktif">Tidak Aktif</option>
                                </select>
                            </div>

                            <label for="foto" class="form-label">Foto Apoteker:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                            </div>

                            <button class="mt-4 btn btn-primary" type="submit">Daftarkan Apoteker</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
@if(session('sukses_addapoteker'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: "{{ session('sukses_addapoteker') }}",
        showConfirmButton: false,
        timer: 1500
    });
</script>
@endif

@if(session('gagal_addapoteker'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: "{{ session('gagal_addapoteker') }}",
    });
</script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const form = document.getElementById('apotekerForm');
        const submitButton = form.querySelector("button[type=submit]");
        const originalAction = form.action;

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                submitButton.innerHTML = "Update data Apoteker";
                const apotekerId = this.getAttribute('data-id');
                fetch(`/apoteker/${apotekerId}`)
                    .then(response => response.json())
                    .then(data => {
                        form.action = "{{ route('updateApoteker') }}";
                        document.getElementById('apoteker_id').value = data.id;
                        document.getElementById('nama_lengkap').value = data.nama_lengkap;
                        document.getElementById('nik').value = data.nik;
                        document.getElementById('email').value = data.email;
                        document.getElementById('No_hp').value = data.No_hp;
                        document.getElementById('alamat_lengkap').value = data.alamat_lengkap;
                        document.getElementById('no_SIPA').value = data.no_SIPA;
                        document.getElementById('Gol_darah').value = data.Gol_darah;
                        document.getElementById('tanggal_lahir').value = data.tanggal_lahir;
                        document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                        document.getElementById('umur').value = data.umur;
                        document.getElementById('Status_apoteker').value = data.Status_apoteker;
                        document.getElementById('password').value = "";

                        const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
                        modal.show();
                    })
                    .catch(error => console.error('Error fetching apoteker data:', error));
            });
        });

        document.getElementById('exampleModal').addEventListener('hidden.bs.modal', function() {
            form.action = originalAction;
            submitButton.innerHTML = "Daftarkan Apoteker";
            form.reset();
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const apotekerId = this.getAttribute('data-id');
                fetch(`/hapusapoteker/${apotekerId}`, {
                        method: 'GET'
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            throw new Error('Gagal menghapus apoteker');
                        }
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal menghapus apoteker!',
                        });
                    });
            });
        });
    });
</script>
@endsection