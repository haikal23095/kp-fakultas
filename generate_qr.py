#!/usr/bin/env python3
"""
Script untuk generate QR Code
Dipanggil dari Laravel untuk membuat QR Code digital signature

Usage:
    python generate_qr.py <data> <output_path> [size]
    
Arguments:
    data         : Data yang akan di-encode ke QR (URL, text, dll)
    output_path  : Path lengkap untuk simpan file PNG
    size         : Optional, ukuran box_size (default 10)
    
Example:
    python generate_qr.py "https://example.com/verify/abc123" "storage/qr/qr_123.png" 10
"""

import sys
import os
import qrcode
from pathlib import Path

def generate_qr_code(data, output_path, box_size=10):
    """
    Generate QR Code dan simpan ke file
    
    Args:
        data: String data yang akan di-encode
        output_path: Path file output (PNG)
        box_size: Ukuran pixel per box (default 10)
    
    Returns:
        bool: True jika berhasil, False jika gagal
    """
    try:
        # Setup QR Code
        qr = qrcode.QRCode(
            version=1,                                      # Ukuran matriks (auto-adjust)
            error_correction=qrcode.constants.ERROR_CORRECT_H,  # High error correction (30%)
            box_size=box_size,                              # Ukuran pixel per kotak
            border=4,                                       # Border putih (4 kotak)
        )
        
        # Tambahkan data
        qr.add_data(data)
        qr.make(fit=True)  # Auto-adjust version sesuai ukuran data
        
        # Generate image (hitam-putih, high contrast)
        img = qr.make_image(fill_color="black", back_color="white")
        
        # Pastikan direktori output ada
        output_dir = Path(output_path).parent
        output_dir.mkdir(parents=True, exist_ok=True)
        
        # Simpan file
        img.save(output_path)
        
        return True
        
    except Exception as e:
        print(f"ERROR: {str(e)}", file=sys.stderr)
        return False

def main():
    # Validasi arguments
    if len(sys.argv) < 3:
        print("Usage: python generate_qr.py <data> <output_path> [box_size]", file=sys.stderr)
        print("Example: python generate_qr.py 'https://example.com' 'qr.png' 10", file=sys.stderr)
        sys.exit(1)
    
    # Parse arguments
    data = sys.argv[1]
    output_path = sys.argv[2]
    box_size = int(sys.argv[3]) if len(sys.argv) > 3 else 10
    
    # Validasi input
    if not data or data.strip() == "":
        print("ERROR: Data tidak boleh kosong", file=sys.stderr)
        sys.exit(1)
    
    if not output_path or output_path.strip() == "":
        print("ERROR: Output path tidak boleh kosong", file=sys.stderr)
        sys.exit(1)
    
    # Generate QR Code
    success = generate_qr_code(data, output_path, box_size)
    
    if success:
        # Output path untuk Laravel baca
        print(output_path)
        sys.exit(0)
    else:
        sys.exit(1)

if __name__ == "__main__":
    main()
