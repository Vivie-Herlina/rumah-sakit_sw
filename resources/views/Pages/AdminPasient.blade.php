@extends("Layout.Admin_layout")
@section("admin")
<style>
    td,
    th {
        text-align: center;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card" style="width: max-content;">
                <div class="card-body">
                    <h5 class="card-title">Data Pasient di {{ Auth::user()->nama_lengkap }}</h5>
                    <button type="button" class="btn btn-primary mt-4 mb-4" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Tambah Pasient
                    </button>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Pasient</th>
                                    <th>Email</th>
                                    <th>No Hp</th>
                                    <th>Alamat</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Foto</th>
                                    <th>Status Pasient</th>
                                    <th>Nomor Antiran</th>
                                    <th>Tujuan Spesialis</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @if($pasient != null)
                                @foreach($pasient as $apoteker)
                                <tr data-id="{{ $apoteker->user->id }}">
                                    <td>{{ $apoteker->user->nama_lengkap }}</td>
                                    <td>{{ $apoteker->user->email }}</td>
                                    <td>{{ $apoteker->user->No_hp }}</td>
                                    <td>{{ $apoteker->alamat_lengkap }}</td>
                                    <td>{{ $apoteker->user->tanggal_lahir }}</td>
                                    <td><img src="{{ asset('asset/img/userfoto/' . $apoteker->user->foto) }}" alt="Foto pasient" style="width: 50px; height: 50px;"></td>
                                    <td class="status">{{ $apoteker->status }}</td>
                                    <td>{{ $apoteker->nomor_antrian }}</td>
                                    <td>{{ $apoteker->spesialis->nama }}</td>
                                    <td>{{ $apoteker->created_at }}</td>
                                    <td>
                                        <button class="btn btn-primary call" type="button" data-id="{{ $apoteker->id }}">Panggil</button>
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
                        <form id="apotekerForm" action="/offlinePasient" method="post" class="d-flex p-4 flex-column" enctype="multipart/form-data">
                            @csrf
                            <label for="nama_lengkap" class="form-label">Nama Pasient :</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                                <input type="text" class="form-control" placeholder="Nama Lengkap Apoteker" name="nama_lengkap" id="nama_lengkap" required>
                            </div>

                            <label for="nik" class="form-label">No NIK Paseint:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" class="form-control" placeholder="NIK Apoteker" name="nik" id="nik" required>
                            </div>

                            <label for="email" class="form-label">Email paseint:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-envelope-heart-fill"></i></span>
                                <input type="email" class="form-control" placeholder="Email Apoteker" name="email" id="email">
                            </div>

                            <label for="No_hp" class="form-label">No.Hp Paseint:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" placeholder="No.hp Apoteker" name="No_hp" id="No_hp">
                            </div>

                            <label for="alamat_lengkap" class="form-label">Alamat Lengkap Pasient:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                <textarea class="form-control" placeholder="Alamat Lengkap Apoteker" name="alamat_lengkap" id="alamat_lengkap"></textarea>
                            </div>
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir Pasient:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-calendar-heart"></i></span>
                                <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir">
                            </div>
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin Pasient:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                <select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required>
                                    <option selected>Pilih Jenis Kelamin</option>
                                    <option value="1">Laki-laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>
                            <label for="password" class="form-label">Password Untuk akun HealthBridge Pasien:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" placeholder="Password" name="password" id="password" required>
                                <span class="input-group-text"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <label for="Status_apoteker" class="form-label">Spesialis Yang dituju:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                <select class="form-select" name="Status_apoteker" id="id_spesialist" required>
                                    <option selected>Pilih Spesialis</option>
                                    @if($spesialist !==null)
                                    @foreach($spesialist as $layanan)
                                    <option value="{{$layanan->id}}">{{$layanan->nama}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="tanggal_lahir" class="form-label">Tanggal berobat Paseint:</label>
                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-calendar-heart"></i></span>
                                <input type="date" class="form-control" name="created_at" id="tanggal_lahir">
                            </div>
                            <button class="mt-4 btn btn-primary" type="submit">Daftarkan Pasient</button>
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
        const callButtons = document.querySelectorAll('.call');
        const editButtons = document.querySelectorAll('.edit-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const form = document.getElementById('apotekerForm');
        const submitButton = form.querySelector("button[type=submit]");
        const originalAction = form.action;

        callButtons.forEach(button => {
            button.addEventListener('click', function() {
                const dokterId = this.getAttribute('data-id');
                alert("test");
                fetch(`/api/callDokter/${dokterId}`, {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'dipanggil'
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            throw new Error('berhasil');
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
                        document.querySelector(`tr[data-id="${dokterId}"] .status`).textContent = 'Dipanggil';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'success',
                            title: 'success',
                            text: 'berhasil',
                        });
                    });
            });
        });

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