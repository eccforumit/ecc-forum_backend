<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpportunityRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Services\Opportunities\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OpportunityController extends Controller
{
    public function __construct(private readonly OpportunityService $opportunities)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $results = $this->opportunities->list($request->only(['status', 'employment_type', 'search', 'per_page']));

        return OpportunityResource::collection($results)
            ->additional(['success' => true])
            ->response();
    }

    public function store(OpportunityRequest $request): OpportunityResource
    {
        $companyProfile = $request->user()->companyProfile;

        if (!$companyProfile) {
            throw new HttpException(403, 'Seules les entreprises peuvent publier des opportunités.');
        }

        $opportunity = $this->opportunities->create($companyProfile, $request->validated());

        return new OpportunityResource($opportunity->load('companyProfile'));
    }

    public function show(Opportunity $opportunity): OpportunityResource
    {
        return new OpportunityResource($opportunity->load('companyProfile'));
    }

    public function update(OpportunityRequest $request, Opportunity $opportunity): OpportunityResource
    {
        $companyProfile = $request->user()->companyProfile;
        if (!$companyProfile || $companyProfile->id !== $opportunity->company_profile_id) {
            throw new AccessDeniedHttpException('Vous ne pouvez modifier que vos propres opportunités.');
        }

        $updated = $this->opportunities->update($opportunity, $request->validated());

        return new OpportunityResource($updated->load('companyProfile'));
    }
}
