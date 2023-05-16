@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"/>
<style>
	.progress-bar{
		background: black;
	}
	.bg-success, .bg-success>a {
    	color: #fff!important;
	}
	.bg-danger, .bg-danger>a {
    	color: #fff!important;
	}
	.bg-primary, .bg-primary>a {
    	color: #fff!important;
	}
	.bg-warning, .bg-warning>a {
    	color: #fff!important;
	}
	.bg-info, .bg-info>a {
    	color: #fff!important;
	}
	.bg-secondary, .bg-secondary>a {
    	color: #fff!important;
	}
	.info-box {
		box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
		border-radius: 0.25rem;
		background-color: #fff;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		margin-bottom: 1rem;
		min-height: 80px;
		padding: 0.5rem;
		position: relative;
		width: 100%;
	}
	.info-box-text{
		font-size: 20px;
		font-weight: bold;
	}
	.info-box-number{
		font-size: 18px;
		font-weight: bold;
	}
</style>
@endpush
@section('content')
<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white fw-bold">Dashboard</h2>
								<h5 class="text-white">Sistemas de verificación de referencias</h5>
							</div>
							
						</div>
					</div>
				</div>
				<div class="panel panel-body page-inner mt--5">
				    <div class="card full-height">
						<div class="card-body">
						</div>
					</div>
				</div>
@endsection

@push('scripts-custom')

@endpush
