<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spesialis;
use App\Models\Fasyankes;
use App\Http\Controllers\AuthUsers;
use App\Models\Apoteker;
use Illuminate\Support\Facades\Hash;
use App\Models\Dokter;
use App\Models\Pendaftaranpasient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private $auth;
    public function __construct()
    {
        $this->auth = new AuthUsers();
    }
    public function Createdokter(Request $request)
    {
        return $this->auth->Adddokter($request);
    }

    public function adminapoteker()
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();

        $data = [
            "tenaga_apoteker" => Apoteker::with("fasyankes")->with("user")->where("id_rumah_sakit", $getdatafasyankes->id)->get(),
            "rumahsakit" => $getdatafasyankes->id
        ];
        return view("pages.adminApoteker", $data);
    }
    public function admindokter()
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();

        $data = [
            "spesialis" => Spesialis::with("Fasyankes")->where("id_rumah_sakit", $getdatafasyankes->id)->get(),
            "tenaga_dokter" => Dokter::with("fasyankes")->with("user")->with("spesialis")->where("id_fasyankes", $getdatafasyankes->id)->get()
        ];
        return view("pages.Admindokter", $data);
    }
    public function deletedokter($id)
    {
        $user = User::findOrFail($id);
        if ($user->delete()) {
            Dokter::where("id_user", $id)->delete();
            return response()->json(['message' => 'Dokter berhasil dihapus'], 200);
        } else {
            return response()->json(['error' => 'Gagal menghapus dokter'], 500);
        }
    }
    public function deleteapoteker($id)
    {
        $user = User::findOrFail($id);
        if ($user->delete()) {
            Apoteker::where("id_user", $id)->delete();
            return response()->json(['message' => 'Dokter berhasil dihapus'], 200);
        } else {
            return response()->json(['error' => 'Gagal menghapus dokter'], 500);
        }
    }
    public function GetApoteker($id)
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();

        $dokter = Apoteker::with(['user', 'fasyankes'])
            ->where('id_rumah_sakit', $getdatafasyankes->id)
            ->where('id_user', $id)
            ->first();

        if (!$dokter) {
            return response()->json(['error' => 'Dokter not found'], 404);
        }

        return response()->json([
            'id' => $dokter->user->id,
            'nama_lengkap' => $dokter->user->nama_lengkap,
            'nik' => $dokter->user->nik,
            'email' => $dokter->user->email,
            'No_hp' => $dokter->user->No_hp,
            'alamat_lengkap' => $dokter->alamat_lengkap,
            'no_SIPA' => $dokter->no_SIPA,
            'Gol_darah' => $dokter->Gol_darah,
            'tanggal_lahir' => $dokter->user->tanggal_lahir,
            'jenis_kelamin' => $dokter->user->jenis_kelamin,
            'umur' => $dokter->umur,
            'Status_apoteker' => $dokter->Status_apoteker,
        ]);
    }
    public function GetDokter($id)
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();

        $dokter = Dokter::with(['fasyankes', 'user', 'spesialis'])
            ->where('id_fasyankes', $getdatafasyankes->id)
            ->where('id_user', $id)
            ->first();

        if (!$dokter) {
            return response()->json(['error' => 'Dokter not found'], 404);
        }

        return response()->json([
            'id' => $dokter->user->id,
            'nama_lengkap' => $dokter->user->nama_lengkap,
            'nik' => $dokter->user->nik,
            'email' => $dokter->user->email,
            'No_hp' => $dokter->user->No_hp,
            'alamat_lengkap' => $dokter->alamat_lengkap,
            'no_SIP' => $dokter->no_SIP,
            'Gol_darah' => $dokter->Gol_darah,
            'tanggal_lahir' => $dokter->user->tanggal_lahir,
            'jenis_kelamin' => $dokter->user->jenis_kelamin,
            'id_spesialis' => $dokter->spesialis->id,
            'umur' => $dokter->umur,
            'Status_dokter' => $dokter->Status_dokter,
        ]);
    }
    // spesialis
    public function Spesialis()
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();

        $data = [
            "spesialis" => Spesialis::with('fasyankes')->where("id_rumah_sakit", $getdatafasyankes->id)->get(),
            "rumahsakit" => $getdatafasyankes->id

        ];
        return view('Pages.AdminSpesialis', $data);
    }
    private function generateQrCode($spesialis)
    {
        $qrContent = "layanan: {$spesialis->id},id_fasyankes: {$spesialis->fasyankes->id}
        ";
        $qrCode = new QrCode($qrContent);

        $writer = new PngWriter();
        $fileName = Str::random(10) . '.png';
        $filePath = public_path('asset/img/qrcode/' . $fileName);

        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $result = $writer->write($qrCode);
        $result->saveToFile($filePath);

        return $fileName;
    }

    public function Addspesialis(Request $request)
    {

        // Validate the incoming request data
        $validated = $request->validate([
            'id_rumah_sakit' => 'required|exists:fasyankes,id',
            'nama' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            if ($request->id == null) {
                // Create a new Spesialis
                $spesialis = Spesialis::create([
                    'id_rumah_sakit' => $validated['id_rumah_sakit'],
                    'nama' => $validated['nama']
                ]);

                $message = 'Spesialis created successfully';
                $route = back();
            } else {
                $spesialis = Spesialis::findOrFail($request->id);
                $spesialis->update($validated);
                $message = 'Spesialis updated successfully';
                $route = back();
            }

            // Generate and update QR code
            $qrCode = $this->generateQrCode($spesialis);
            $spesialis->update(['qr_code' => $qrCode]);

            DB::commit();
            return $route->with('successqr', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $request->id == null ? 'Error creating spesialis: ' : 'Error updating spesialis: ';
            return back()->withErrors($errorMessage . $e->getMessage());
        }
    }

    public function hapuslayanan($id)
    {
        $spesialis = Spesialis::findOrFail($id);
        $spesialis->delete();
        return redirect()->back()->with('success', 'Spesialis deleted successfully');
    }
    public function Gelayanan($id)
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();
        $dokter = Spesialis::with('fasyankes')->where("Spesialis.id", $id)->first();

        if (!$dokter) {
            return response()->json(['error' => 'Dokter not found'], 404);
        }

        return response()->json([
            'id' => $dokter->id,
            'nama' => $dokter->nama
        ]);
    }
    public function adminpasient()
    {
        $getdatafasyankes = Fasyankes::where("id_users", Auth::user()->id)->first();
        $data = [
            "pasient" => Pendaftaranpasient::with("fasyankes")->with("user")->with('spesialis')->where("id_fasyankes", $getdatafasyankes->id)->get(),
            "rumahsakit" => $getdatafasyankes->id,
            "spesialist" => Spesialis::where("id_rumah_sakit", $getdatafasyankes->id)->get()
        ];
        return view("pages.AdminPasient", $data);
    }
    public function offlinePasient(Request $request)
    {
        try {
            $validate = $request->validate([
                "nama_lengkap" => "required|min:5",
                "No_hp" => "required|min:12",
                "nik" => "required|min:16",
                "tanggal_lahir" => "required|date",
                "password" => ['required', Password::min(8)->letters()->mixedCase()->numbers()],
                "jenis_kelamin" => "required",
                "email" => "required|email:rfc,dns|unique:users",
                "created_at" => "required|date",
                "id_spesialis" => "required"
            ]);

            Log::info('Validation passed for creating new patient', $validate);

            $validate["role"] = "pasien";
            $id_fasyankes = Fasyankes::where("id_user", Auth::user()->id)->first();
            if (!$id_fasyankes) {
                Log::error('Fasyankes not found for user', ['user_id' => Auth::user()->id]);
                return back()->withErrors(['error' => 'Fasyankes not found for the current user.']);
            }

            $validate["id_fasyankes"] = $id_fasyankes->id;
            $validate["foto"] = "pasient.svg";
            $validate["password"] = Hash::make($request->password);

            $pasient = User::create($validate);

            if ($pasient) {
                Log::info('New patient created successfully', ['user_id' => $pasient->id]);

                $id_fasyankes = $id_fasyankes->id;
                $id_spesialis = $validate["id_spesialis"];
                $today = Carbon::today();
                $existingEntry = Pendaftaranpasient::where('id_fasyankes', $id_fasyankes)
                    ->where('id_spesialis', $id_spesialis)
                    ->where('id_user', $pasient->id)
                    ->whereDate('created_at', $today)
                    ->first();

                if ($existingEntry) {
                    Log::warning('Patient already registered today for the same specialist', ['user_id' => $pasient->id, 'id_spesialis' => $id_spesialis]);
                    return back()->with("terdaftar", "Anda sudah terdaftar hari ini untuk spesialis ini.");
                }

                $antrianCount = Pendaftaranpasient::where('id_fasyankes', $id_fasyankes)
                    ->where('id_spesialis', $id_spesialis)
                    ->whereDate('created_at', $today)
                    ->count();
                $nomor_antrian = $antrianCount + 1;

                $newpasient = new Pendaftaranpasient();
                $newpasient->id_user = $pasient->id;
                $newpasient->id_fasyankes = $id_fasyankes;
                $newpasient->id_spesialis = $id_spesialis;
                $newpasient->nomor_antrian = $nomor_antrian;
                $newpasient->created_at = $validate["created_at"];
                $newpasient->save();

                Log::info('New registration entry created', ['user_id' => $pasient->id, 'nomor_antrian' => $nomor_antrian]);

                return back()->with("sukses", "Pasient berhasil didaftarkan.");
            }

            Log::error('Failed to create patient');
            return back()->withErrors(['error' => 'Gagal mendaftarkan pasient.']);
        } catch (\Exception $e) {
            Log::error('An error occurred in offlinePasient method', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftarkan pasient.']);
        }
    }
}
