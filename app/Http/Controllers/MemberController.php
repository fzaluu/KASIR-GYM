<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index()
    {
        $allMember = Member::with('absensi')->get();
        foreach ($allMember as $member) {
            $hariIni = Carbon::today();
            $tanggalExpired = Carbon::parse($member->tanggal_kadaluarsa);

            if ($hariIni->gt($tanggalExpired)) {
                $member->sisa_hari = "HABIS";
            } else {
                $member->sisa_hari = $hariIni->diffInDays($tanggalExpired) . " Hari Lagi";
            }
        }
        return view('member', [
            'daftarMember' => $allMember
        ]);
    }
        public function store(Request $request)
        {
            $request->validate([
                'nama_member' => 'required|string|max:100',
                'nomer_telepon' => 'required|string|max:20',
                'tanggal_kadaluarsa' => 'required|date'
            ]);
            member::create([
                'nama_member' => $request->nama_member,
                'nomer_telepon' => $request->nomer_telepon,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa
            ]);
            return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan.');
        }
    }
