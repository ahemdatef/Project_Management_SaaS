export default function TenantHome({ params }) {
  return (
    <div className="p-10">
      <h1 className="text-4xl font-bold">Welcome to {params.site}</h1>
      <p>This is a tenant-specific dashboard.</p>
    </div>
  );
}
