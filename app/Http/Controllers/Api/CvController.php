<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CvStoreRequest;
use App\Http\Requests\CvUpdateRequest;
use App\Http\Resources\CvDocumentResource;
use App\Models\CvDocument;
use App\Services\Cv\CvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CvController extends Controller
{
    public function __construct(private readonly CvService $cvs)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $results = $this->cvs->search($request->only(['school', 'major', 'skill', 'keyword', 'per_page']));

        return CvDocumentResource::collection($results)
            ->additional(['success' => true])
            ->response();
    }

    public function store(CvStoreRequest $request): CvDocumentResource
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (!$profile) {
            throw new HttpException(403, 'Seuls les étudiants peuvent enregistrer un CV.');
        }

        $document = $this->cvs->store($profile, $request->validated());

        return new CvDocumentResource($document->load('studentProfile'));
    }

    public function show(CvDocument $cvDocument): CvDocumentResource
    {
        return new CvDocumentResource($cvDocument->load('studentProfile'));
    }

    public function update(CvUpdateRequest $request, CvDocument $cvDocument): CvDocumentResource
    {
        if ($request->user()->studentProfile?->id !== $cvDocument->student_profile_id) {
            throw new AccessDeniedHttpException('Vous ne pouvez modifier que vos propres CV.');
        }

        $document = $this->cvs->update($cvDocument, $request->validated());

        return new CvDocumentResource($document->load('studentProfile'));
    }
}
