Often happens that PATH on Windows has duplicates. And it's of limited length - max 1024 characters. This script can remove duplicates and help you clean-up PATH.

## Requirements

* Windows
* PHP

## Usage

```bash
> php PathManagement.php split
```

It will read the %PATH% and split it into individual folders. Normalize it by removing '\\' in the end, sort and save to the path.txt file.

Please review path.txt file, made changes if necessary.

```bash
> php PathManagement.php merge
```

It will read the path.txt file, merge all paths and set it into Windows path variable with ```setx``` command (persistent).

Enjoy!
