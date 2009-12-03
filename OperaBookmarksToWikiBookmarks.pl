#!/usr/bin/perl

use strict;
use POSIX qw(strftime);

# устройство заголовков
my $split = [
    #'$folder',
    #'%Y',
    '%B %Y',
    #'%d.%m.%Y',
];
my $datef = '[$folder] %d %B, %H:%M:%S: ';

my $st = '';
my $bookmarks = [];
my $bm = {};
my $folder = '';
my $created = undef;
while(<>)
{
    s/\s+$//so;
    if ($_ eq '#FOLDER' || $_ eq '#URL')
    {
        $st = $_;
        if ($_ eq '#URL')
        {
            if ($bm->{created})
            {
                $bm->{folder} = $folder;
                push @$bookmarks, $bm;
            }
            $bm = {};
        }
        else
        {
            $folder = '';
            $created = undef;
        }
        if ($folder && !$created)
        {
            $folder = '';
        }
    }
    elsif (s/^\s+//so)
    {
        my ($key, $value) = split /=/, $_, 2;
        $key = lc $key;
        if ($st eq '#FOLDER')
        {
            if ($key eq 'name')
            {
                $folder = $value;
            }
            elsif ($key eq 'created')
            {
                $created = $value;
            }
        }
        elsif ($st eq '#URL')
        {
            $bm->{$key} = $value;
        }
    }
}
if ($bm->{created})
{
    push @$bookmarks, $bm;
    $bm->{folder} = $folder;
}

#use Data::Dumper;
#print Dumper $bookmarks;

sub sortfoldertime { ($a->{folder} cmp $b->{folder}) || ($b->{created} <=> $a->{created}) }
sub sorttime { $b->{created} <=> $a->{created} }

my $curhead = [];
my $curhl = [(2) x @$split];
my $h;
my $hl;
my $sort = $split->[0] =~ /\$folder/ ? \&sortfoldertime : \&sorttime;
foreach my $bm (sort $sort @$bookmarks)
{
    for my $i (0..$#$split)
    {
        if ($curhead->[$i] ne h($bm, $split->[$i]))
        {
            $hl = $curhl->[$i];
            for my $j ($i..$#$split)
            {
                $curhead->[$j] = $h = h($bm, $split->[$j]);
                $curhl->[$j] = $hl;
                if ($h)
                {
                    print ("\n" . ('=' x $hl) . ' ' . $h . ' ' . ('=' x $hl) . "\n");
                    $hl++;
                }
            }
            print "\n";
        }
    }
    print "* ".h($bm, $datef)."[$bm->{url} $bm->{name}]\n";
}

sub h
{
    my ($bm, $f) = @_;
    $f =~ s/\$folder/$bm->{folder}/gso;
    $f =~ s/^\s*\[\s*\]\s*//;
    return strftime($f, localtime($bm->{created}));
}
