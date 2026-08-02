@extends('layouts.admin',['title'=>'Penugasan Akademik','heading'=>'Penugasan Akademik'])
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
@foreach([
['student_classroom','Tempatkan Siswa',['student_id'=>$students,'classroom_id'=>$classrooms,'academic_year_id'=>$academicYears],['status'=>['active','promoted','moved']]],
['parent_student','Hubungkan Orang Tua',['parent_profile_id'=>$parents,'student_id'=>$students],['is_primary'=>['0','1']]],
['teacher_subject','Tugaskan Guru Mapel',['teacher_id'=>$teachers,'subject_id'=>$subjects,'academic_year_id'=>$academicYears],[]],
['homeroom','Tetapkan Wali Kelas',['teacher_id'=>$teachers,'classroom_id'=>$classrooms],[]]
] as [$type,$heading,$relations,$selects])
<form method="POST" action="{{ route('admin.academic.assignments.store') }}" class="rounded-2xl bg-white p-6 shadow-sm">@csrf<input type="hidden" name="type" value="{{ $type }}"><h2 class="text-lg font-bold">{{ $heading }}</h2><div class="mt-5 space-y-4">
@foreach($relations as $name=>$options)<label class="block text-sm font-semibold">{{ str($name)->replace(['_id','_'],' ')->title() }}<select name="{{ $name }}" required class="mt-2 w-full rounded-xl border-slate-200"><option value="">Pilih</option>@foreach($options as $option)<option value="{{ $option->id }}">{{ $option->name }}{{ isset($option->students_count)?' ('.$option->students_count.' siswa)':'' }}</option>@endforeach</select></label>@endforeach
@foreach($selects as $name=>$options)<label class="block text-sm font-semibold">{{ str($name)->replace('_',' ')->title() }}<select name="{{ $name }}" class="mt-2 w-full rounded-xl border-slate-200">@foreach($options as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</select></label>@endforeach
<button class="w-full rounded-full bg-emerald-700 px-5 py-3 font-bold text-white">Simpan</button></div></form>
@endforeach
</div>
<section class="mt-6 rounded-2xl bg-white p-6 shadow-sm"><div class="flex flex-wrap gap-3"><a href="{{ route('admin.academic.timetable') }}" class="rounded-full bg-emerald-700 px-5 py-2 font-semibold text-white">Timetable responsif</a>@can('export_academic')<a href="{{ route('admin.academic.export','students') }}" class="rounded-full border px-5 py-2 font-semibold">Export siswa Excel</a><a href="{{ route('admin.academic.export','teachers') }}" class="rounded-full border px-5 py-2 font-semibold">Export guru Excel</a>@endcan</div>
@can('import_academic')<form method="POST" action="{{ route('admin.academic.students.import') }}" enctype="multipart/form-data" class="mt-5 flex flex-col gap-3 sm:flex-row">@csrf<input type="file" name="file" accept=".xlsx,.xls,.csv" required class="flex-1"><button class="rounded-full bg-slate-900 px-5 py-2 font-bold text-white">Import siswa Excel</button></form>@endcan</section>
@endsection
