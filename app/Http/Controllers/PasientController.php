<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthUsers;
use App\Models\Fasyankes;
use App\Models\Pendaftaran_pasient;
use App\Models\Pendaftaranpasient;
use App\Models\Spesialis;
use Illuminate\Support\Facades\Auth;
use Zxing\QrReader;
use Carbon\Carbon;


class PasientController extends Controller
{
    private $auth;
    public function __construct()
    {
        $this->auth = new AuthUsers();
    }
    public function updateCreate(Request $request)
    {
        return $this->auth->pasientRegister($request);
    }
    public function scan(Request $request)
    {
        $qrCodePath = $request->file('qr_code')->getPathname();
        $qrcode = new QrReader($qrCodePath);
        $decodedText = $qrcode->text();

        return view('Pages.scan', compact('decodedText'));
    }

    public function saveScannedData(Request $request)
    {
        $request->session()->put('scannedData', $request->scannedData);
        return response()->json(['message' => 'Data saved successfully'], 200);
    }
    private function convertToAssocArray($data)
    {
        $items = explode(',', $data);
        $assocArray = array();

        foreach ($items as $item) {
            list($key, $value) = explode(':', $item);
            $assocArray[trim($key)] = trim($value);
        }

        return $assocArray;
    }

    public function showSpesialis()
    {
        $scannedData = session('scannedData', 'layanan: 17,id_fasyankes: 4');
        $scannedDataArray = $this->convertToAssocArray($scannedData);
        $id_fasyankes = $scannedDataArray["id_fasyankes"];
        $id_spesialis = $scannedDataArray["layanan"];
        $today = Carbon::today();
        $existingEntry = Pendaftaranpasient::where('id_fasyankes', $id_fasyankes)
            ->where('id_spesialis', $id_spesialis)
            ->where('id_user', Auth::user()->id)
            ->whereDate('created_at', $today)
            ->first();
        if ($existingEntry) {
            return back()->with("terdaftar", "Anda sudah terdaftar hari ini untuk spesialis ini.");
        }

        $antrianCount = Pendaftaranpasient::where('id_fasyankes', $id_fasyankes)
            ->where('id_spesialis', $id_spesialis)
            ->whereDate('created_at', $today)
            ->count();
        $nomor_antrian = $antrianCount + 1;
        $newpasient = new Pendaftaranpasient();
        $newpasient->id_user = Auth::user()->id;
        $newpasient->id_fasyankes = $id_fasyankes;
        $newpasient->id_spesialis = $id_spesialis;
        $newpasient->nomor_antrian = $nomor_antrian;
        if ($newpasient->save()) {
            return back()->with("berhasil_scan", "Antrian berhasil ditambahkan.");
        } else {
            return back()->with("error", "Terjadi kesalahan saat menambahkan antrian.");
        }
    }
    public function Antrianpage()
    {
        $getantiran = Pendaftaranpasient::where("id_user", Auth::user()->id)->first();
        $data = [
            "rumahsakit" => Fasyankes::with('user')->where("id", $getantiran->id_fasyankes)->get(),
            "antrian" => $getantiran->nomor_antrian,
            "tanggal_antrian" => $getantiran->created_at
        ];
        return view("Pages.Antrianpassien", $data);
    }
    public function checkStatus($id)
    {
        $user = Pendaftaranpasient::where("id_user", $id)->first();
        return response()->json(['status' => $user->status]);
    }
    public function callDokter(Request $request, $id)
    {
        $dokter = Pendaftaranpasient::find($id);
        if ($dokter) {
            $dokter->status = $request->input('status');
            $dokter->save();
            return response()->json(['message' => 'Status pasient berhasil diperbarui'], 200);
        } else {
            return response()->json(['message' => 'pasient tidak ditemukan'], 404);
        }
    }
}
