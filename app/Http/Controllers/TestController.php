<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestController extends Controller
{
    function __construct()
    {
    }

    public function register(Request $req)
    {
        $this->validate($req, [
            "firstname" => "required",
            "lastname" => "required",
            "dob" => "required|date|before:today",
            "photo" => "nullable|image"
        ]);

        $path = null;
        if ($req->hasFile('photo')) {
            $file = $req->file('photo');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('files' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . date('Gis'), $filename);
        }

        $user = User::create([
            'firstname' => $req->input('firstname'),
            'lastname' => $req->input('lastname'),
            'dob' => $req->input('dob'),
            'address' => $req->input('address'),
            'photo' => $path,
        ]);

        return response()->json([
            'code' => Response::HTTP_OK,
            'data' => $user
        ]);
    }

    public function login(Request $req)
    {
        return response()->json([
            'code' => Response::HTTP_OK,
            'data' => 'Response'
        ]);
    }

    public function users(Request $req)
    {
        $pagination = new \stdClass();
        $pagination->page = $req->query("page") ?: 1;
        $pagination->limit = $req->query("limit") ?: 10;
        $pagination->sort = ["created_at", "desc"];
        if ($req->filled("sort")) {
            $pagination->sort = explode(",", $req->query("sort"));
        }

        $data = User::select('*');

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy($sort[0], $sort[1]);
        }

        if (!empty($req->query('search'))) {
            $data->where(function ($q) use ($req) {
                $q->where("firstname", "ILIKE", "%{$req->query("search")}%");
                $q->orWhere("lastname", "ILIKE", "%{$req->query("search")}%");
            });
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        return response()->json([
            'code' => Response::HTTP_OK,
            'data' => $data
        ]);
    }

    public function userDetail($id, Request $req)
    {
        $data = User::where('id', $id);

        if (empty($data->count())) {
            throw new NotFoundHttpException("user not found.");
        }

        $data = $data->first();

        return response()->json([
            'code' => Response::HTTP_OK,
            'data' => $data
        ]);
    }
}
