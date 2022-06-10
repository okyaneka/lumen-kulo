<?php

namespace App\Http\Controllers;

use App\Models\Filestore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestController extends Controller
{
    function __construct()
    {
        // $this->middleware('auth:api', ['only' => ['users', 'userDetail']]);
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
            $filestore = Filestore::store($file);
            $path = $filestore->path;
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
        $credentials = $req->only(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = Auth::user();
        $data->token = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];

        return response()->json([
            'code' => Response::HTTP_OK,
            'data' => $data
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
