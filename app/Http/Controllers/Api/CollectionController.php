<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddMediaToCollectionRequest;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function index(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    // Aksi non-CRUD: menautkan Media dengan User melalui media_user.
    public function store(AddMediaToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');

        // Lock per-user menjaga pengecekan duplikat dan attach tetap atomik.
        return DB::transaction(function () use ($user, $mediaId): JsonResponse {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());

            if ($lockedUser->media()->whereKey($mediaId)->exists()) {
                return response()->json([
                    'message' => 'Media sudah ada di koleksi pengguna.',
                ], 409);
            }

            $lockedUser->media()->attach($mediaId);

            return (new UserResource($lockedUser->load('media')))
                ->additional(['message' => 'Media berhasil ditambahkan ke koleksi.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    // Aksi non-CRUD: melepas relasi Media dari koleksi tanpa menghapus Media.
    public function destroy(User $user, Media $media): JsonResponse
    {
        return DB::transaction(function () use ($user, $media): JsonResponse {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());

            if (! $lockedUser->media()->whereKey($media->getKey())->exists()) {
                return response()->json([
                    'message' => 'Media tidak ditemukan dalam koleksi pengguna.',
                ], 404);
            }

            $lockedUser->media()->detach($media->getKey());

            return (new UserResource($lockedUser->load('media')))
                ->additional(['message' => 'Media berhasil dihapus dari koleksi.'])
                ->response();
        });
    }
}
