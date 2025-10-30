import Image from 'next/image';

export function Logo() {
  return (
    <div className="flex items-center">
      <Image 
        src="/logo.png" 
        alt="Vega Movies Logo"
        width={100}
        height={25}
        style={{ height: 'auto', width: '100px' }}
        className="object-contain"
      />
    </div>
  );
}
