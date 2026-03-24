<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/rooms'), $imageName);
        }

        Room::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'facilities' => $request->facilities,
            'image' => $imageName,
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil ditambah!');
    }

    // Munculkan halaman edit
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    // Proses update data ke database
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        $data = $request->all();

        // Jika ada upload foto baru
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($room->image && file_exists(public_path('images/rooms/' . $room->image))) {
                unlink(public_path('images/rooms/' . $room->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/rooms'), $imageName);
            $data['image'] = $imageName;
        }

        $room->update($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil diperbarui!');
    }

    // Proses hapus data
    public function destroy(Room $room)
    {
        // Hapus file fotonya dulu dari folder
        if ($room->image && file_exists(public_path('images/rooms/' . $room->image))) {
            unlink(public_path('images/rooms/' . $room->image));
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil dihapus!');
    }

    public function toggleStatus(Room $room)
    {
        // Jika available jadi full, jika full jadi available
        $newStatus = ($room->status === 'available') ? 'full' : 'available';

        $room->update([
            'status' => $newStatus
        ]);

        return back()->with('success', 'Status kamar ' . $room->name . ' berhasil diubah menjadi ' . $newStatus);
    }
}
